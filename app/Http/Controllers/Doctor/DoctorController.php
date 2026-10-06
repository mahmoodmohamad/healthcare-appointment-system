<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Patient};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ownership checks (view / update) are enforced in routes/web.php
 * through `can:view,appointment` and `can:update,appointment`.
 */
class DoctorController extends Controller
{
    public function appointments(Request $request)
    {
        $doctor = auth()->user()->doctor;

        $query = $doctor->appointments()->with(['patient.user', 'diagnosis']);

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($date = $request->date) {
            $query->whereDate('appointment_date', $date);
        }

        switch ($request->filter) {
            case 'today':
                $query->whereDate('appointment_date', today());
                break;
            case 'upcoming':
                $query->where('appointment_date', '>', now());
                break;
            case 'past':
                $query->where('appointment_date', '<', now());
                break;
        }

        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(15);

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

        // Lock the row so two concurrent submits can't create two diagnoses
        $created = DB::transaction(function () use ($data, $appointment) {
            $locked = Appointment::lockForUpdate()->findOrFail($appointment->id);

            if ($locked->diagnosis()->exists()) {
                return false;
            }

            $locked->diagnosis()->create($data);
            $locked->update(['status' => 'completed']);

            return true;
        });

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