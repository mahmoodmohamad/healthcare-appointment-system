<?php

namespace App\Http\Controllers\Physician;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Diagnosis, Patient};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhysicianController extends Controller
{


    /**
     * View all appointments
     */
    public function appointments(Request $request)
    {
        $physician = auth()->user()->physician;

        $query = $physician->appointments()
            ->with(['patient.user', 'diagnosis']);

        // Filter by status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date = $request->date) {
            $query->whereDate('appointment_date', $date);
        }

        // Filter: today, upcoming, past
        if ($filter = $request->filter) {
            switch ($filter) {
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
        }

        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(15);

        return view('physician.appointments.index', compact('appointments'));
    }

    /**
     * View single appointment
     */
    public function showAppointment(Appointment $appointment)
    {
        

        $appointment->load(['patient.user', 'patient.city', 'diagnosis']);

        return view('physician.appointments.show', compact('appointment'));
    }


    /**
     * Show diagnosis form
     */
    public function createDiagnosis(Appointment $appointment)
    {
        

        if ($appointment->diagnosis) {
            return redirect()
                ->route('physician.appointments.show', $appointment)
                ->with('error', 'This appointment already has a diagnosis.');
        }

        return view('physician.diagnosis.create', compact('appointment'));
    }


    /**
     * Store diagnosis
     */
    public function storeDiagnosis(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        if ($appointment->diagnosis) {
            return redirect()->back()->with('error', 'Diagnosis already exists');
        }

        $request->validate([
            'symptoms' => 'required|string',
            'diagnosis' => 'required|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $appointment) {
            Diagnosis::create([
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'physician_id' => $appointment->physician_id,
                'symptoms' => $request->symptoms,
                'diagnosis' => $request->diagnosis,
                'prescription' => $request->prescription,
                'notes' => $request->notes,
            ]);

            $appointment->update(['status' => 'completed']);
        });

        return redirect()
            ->route('physician.appointments.show', $appointment)
            ->with('success', 'Diagnosis saved successfully!');
    }


    /**
     * View patient medical history
     */
    public function patientHistory(Patient $patient)
    {
        $physician = auth()->user()->physician;

        // Get all appointments for this patient with this physician
        $appointments = $patient->appointments()
            ->where('physician_id', $physician->id)
            ->with(['diagnosis'])
            ->orderByDesc('appointment_date')
            ->get();

        $patient->load(['user', 'city']);

        return view('physician.patients.history', compact('patient', 'appointments'));
    }

    /**
     * Edit diagnosis
     */
    public function editDiagnosis(Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        if (!$appointment->diagnosis) {
            return redirect()
                ->route('physician.appointments.show', $appointment)
                ->with('error', 'No diagnosis found for this appointment.');
        }

        $appointment->load(['patient.user', 'diagnosis']);

        return view('physician.diagnosis.edit', compact('appointment'));
    }


    /**
     * Update diagnosis
     */
    public function updateDiagnosis(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        if (!$appointment->diagnosis) {
            return redirect()
                ->route('physician.appointments.show', $appointment)
                ->with('error', 'No diagnosis found.');
        }

        $request->validate([
            'symptoms' => 'required|string',
            'diagnosis' => 'required|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment->diagnosis->update($request->only([
            'symptoms',
            'diagnosis',
            'prescription',
            'notes',
        ]));

        return redirect()
            ->route('physician.appointments.show', $appointment)
            ->with('success', 'Diagnosis updated successfully!');
    }
}
