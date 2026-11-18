<?php

declare(strict_types=1);

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\IndexAppointmentsRequest;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Ownership checks (view / diagnose) are enforced in routes/web.php
 * through `can:view,appointment` and `can:diagnose,appointment`.
 */
class DoctorController extends Controller
{
    private const PER_PAGE = 15;

    /**
     * List the authenticated doctor's appointments.
     *
     * Date filters compare the DATE part only, because appointment_date
     * is stored with a time by the API but at 00:00 by the web form.
     */
    public function appointments(IndexAppointmentsRequest $request): View
    {
        $doctor = $request->user()->doctor;

        abort_unless($doctor, 403, 'Doctor profile not found.');

        $filters = $request->validated();

        $appointments = $doctor->appointments()
            // Only existence is needed for the diagnosis column
            // (pendingFirst() already adds `diagnosis_exists`).
            ->with('patient.user')
            ->when(
                $filters['status'] ?? null,
                fn ($query, string $status) => $query->where('status', $status)
            )
            ->when(
                $filters['date'] ?? null,
                fn ($query, string $date) => $query->whereDate('appointment_date', $date)
            )
            ->when(
                $filters['filter'] ?? null,
                fn ($query, string $filter) => match ($filter) {
                    IndexAppointmentsRequest::FILTER_TODAY => $query
                        ->whereDate('appointment_date', today()),
                    IndexAppointmentsRequest::FILTER_UPCOMING => $query
                        ->whereDate('appointment_date', '>', today()),
                    IndexAppointmentsRequest::FILTER_PAST => $query
                        ->whereDate('appointment_date', '<', today()),
                }
            )
            ->pendingFirst()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('doctor.appointments.index', [
            'appointments' => $appointments,
            'filters' => $filters,
        ]);
    }

    public function showAppointment(Appointment $appointment): View
    {
        $appointment->load(['patient.user', 'patient.city', 'diagnosis']);

        return view('doctor.appointments.show', compact('appointment'));
    }

    public function createDiagnosis(Appointment $appointment): View|RedirectResponse
    {
        if ($appointment->diagnosis) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->with('error', 'This appointment already has a diagnosis.');
        }

        $appointment->load(['patient.user']);

        return view('doctor.diagnosis.create', compact('appointment'));
    }

    public function storeDiagnosis(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $data = $request->validate([
            'symptoms' => ['required', 'string'],
            'diagnosis' => ['required', 'string'],
            'prescription' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            // Row lock + unique index: two concurrent submits
            // can never create two diagnoses.
            $created = DB::transaction(function () use ($data, $appointment): bool {
                $locked = Appointment::lockForUpdate()->findOrFail($appointment->id);

                if ($locked->diagnosis()->exists()) {
                    return false;
                }

                $locked->diagnosis()->create($data);
                $locked->update(['status' => Appointment::COMPLETED]);

                return true;
            });
        } catch (QueryException $exception) {
            if (! Appointment::isDuplicateKey($exception)) {
                throw $exception;
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

    public function editDiagnosis(Appointment $appointment): View|RedirectResponse
    {
        if (! $appointment->diagnosis) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->with('error', 'No diagnosis found for this appointment.');
        }

        $appointment->load(['patient.user', 'diagnosis']);

        return view('doctor.diagnosis.edit', compact('appointment'));
    }

    public function updateDiagnosis(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        if (! $appointment->diagnosis) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->with('error', 'No diagnosis found.');
        }

        $data = $request->validate([
            'symptoms' => ['required', 'string'],
            'diagnosis' => ['required', 'string'],
            'prescription' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment->diagnosis->update($data);

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('success', 'Diagnosis updated successfully!');
    }

    public function patientHistory(Patient $patient): View
    {
        $doctor = auth()->user()->doctor;

        $appointments = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->with('diagnosis')
            ->orderByDesc('appointment_date')
            ->get();

        // A doctor may only see patients he has treated / is booked with.
        abort_if($appointments->isEmpty(), 403);

        $patient->load(['user', 'city']);

        return view('doctor.patients.history', compact('patient', 'appointments'));
    }
}