<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Patient};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 use Illuminate\Database\QueryException;
/**
 * Ownership checks (view / update) are enforced in routes/web.php
 * through `can:view,appointment` and `can:update,appointment`.
 */
class DoctorController extends Controller
{
   public function appointments(Request $request)
{
    $validated = $request->validate([
        'status' => ['nullable', 'in:scheduled,completed,cancelled'],
        'date'   => ['nullable', 'date'],
        'filter' => ['nullable', 'in:today,upcoming,past'],
    ]);

    $appointments = auth()->user()->doctor
        ->appointments()
        ->with(['patient.user', 'diagnosis'])
        ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
        ->when($validated['date'] ?? null, fn ($q, $date) => $q->whereDate('appointment_date', $date))
        ->when($validated['filter'] ?? null, fn ($q, $filter) => match ($filter) {
            'today'    => $q->whereDate('appointment_date', today()),
            'upcoming' => $q->where('appointment_date', '>', now()),
            'past'     => $q->where('appointment_date', '<', now()),
        })
        ->pendingFirst()
        ->paginate(15)
        ->withQueryString();

    return view('doctor.appointments.index', compact('appointments'));
}
    public function showAppointment(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'patient.city', 'diagnosis']);

        return view('doctor.appointments.show', compact('appointment'));
    }

    public function createDiagnosis(Appointment $appointment)
    {
        if ($appointment->diagnosis) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->with('error', 'This appointment already has a diagnosis.');
        }

        $appointment->load(['patient.user']);

        return view('doctor.diagnosis.create', compact('appointment'));
    }

    public function storeDiagnosis(Request $request, Appointment $appointment)
{
    $data = $request->validate([
        'symptoms'     => 'required|string',
        'diagnosis'    => 'required|string',
        'prescription' => 'nullable|string',
        'notes'        => 'nullable|string',
    ]);

    try {
        // Row lock + unique index: two concurrent submits can't create two diagnoses
        $created = DB::transaction(function () use ($data, $appointment) {
            $locked = Appointment::lockForUpdate()->findOrFail($appointment->id);

            if ($locked->diagnosis()->exists()) {
                return false;
            }

            $locked->diagnosis()->create($data);
            $locked->update(['status' => 'completed']);

            return true;
        });
    } catch (QueryException $e) {
        if (($e->errorInfo[1] ?? null) !== 1062) {
            throw $e;
        }
        $created = false;
    }

    if (! $created) {
        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('error', 'Diagnosis already exists.');
    }

    return redirect()
        ->route('doctor.appointments.show', $appointment)
        ->with('success', 'Diagnosis saved successfully!');
}
    public function editDiagnosis(Appointment $appointment)
    {
        if (! $appointment->diagnosis) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->with('error', 'No diagnosis found for this appointment.');
        }

        $appointment->load(['patient.user', 'diagnosis']);

        return view('doctor.diagnosis.edit', compact('appointment'));
    }

    public function updateDiagnosis(Request $request, Appointment $appointment)
    {
        if (! $appointment->diagnosis) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->with('error', 'No diagnosis found.');
        }

        $data = $request->validate([
            'symptoms'     => 'required|string',
            'diagnosis'    => 'required|string',
            'prescription' => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $appointment->diagnosis->update($data);

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('success', 'Diagnosis updated successfully!');
    }

    public function patientHistory(Patient $patient)
    {
        $doctor = auth()->user()->doctor;

        $appointments = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->with('diagnosis')
            ->orderByDesc('appointment_date')
            ->get();

        // A doctor may only see patients he has treated / is booked with
        abort_if($appointments->isEmpty(), 403);

        $patient->load(['user', 'city']);

        return view('doctor.patients.history', compact('patient', 'appointments'));
    }
}