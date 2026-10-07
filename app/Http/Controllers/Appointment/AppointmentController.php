<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    private const WITH = [
        'patient.user',
        'doctor.user',
        'receptionist.user',
        'diagnosis',
    ];

    /**
     * List appointments.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $user = $request->user();

        $query = Appointment::query()
            ->with(self::WITH)
            ->latest('appointment_date')
            ->latest('appointment_time');

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        }

        $query->when(
            $request->filled('status'),
            fn ($q) => $q->where(
                'status',
                $request->string('status')->toString()
            )
        );

        $query->when(
            $request->filled('date'),
            fn ($q) => $q->whereDate(
                'appointment_date',
                $request->date('date')
            )
        );

        $appointments = $query->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show create appointment form.
     */

public function create(): View
{
    $this->authorize('create', Appointment::class);

    $patients = Patient::with('user')
        ->orderBy('id')
        ->get();

    $doctors = Doctor::with('user')
        ->orderBy('id')
        ->get();

    return view('appointments.create', compact('patients', 'doctors'));
}


    /**
     * Store a new appointment.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Appointment::class);

        $data = $request->validate([
            'patient_id'       => ['required', 'integer', 'exists:patients,id'],
            'doctor_id'        => ['required', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'notes'            => ['nullable', 'string', 'max:5000'],
        ]);

        if (! Appointment::isAvailable(
            $data['doctor_id'],
            $data['appointment_date'],
            $data['appointment_time']
        )) {
            return back()
                ->withErrors([
                    'appointment_time' => 'The selected doctor is not available at this time.',
                ])
                ->withInput();
        }

        Appointment::create([
            ...$data,
            'receptionist_id' => optional($request->user()->receptionist)->id,
            'appointment_date' => Carbon::parse(
                $data['appointment_date'] . ' ' . $data['appointment_time']
            ),
            'status' => Appointment::SCHEDULED,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Show appointment.
     */
    public function show(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);

        $appointment->load(self::WITH);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Update appointment.
     */
    public function update(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $this->authorize('manage', $appointment);

        $user = $request->user();

        $data = $request->validate([
            'patient_id'       => ['sometimes', 'integer', 'exists:patients,id'],
            'doctor_id'        => ['sometimes', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'appointment_time' => ['sometimes', 'date_format:H:i'],
            'status'           => ['sometimes', 'in:scheduled,completed,cancelled'],
            'notes'            => ['nullable', 'string', 'max:5000'],
        ]);

        /*
         * Doctors cannot reassign or reschedule appointments.
         * They can only change status and notes.
         */
        if (! ($user->isAdmin() || $user->isReceptionist())) {
            $data = Arr::only($data, [
                'status',
                'notes',
            ]);
        }

        /*
         * Re-check availability when doctor/date/time changes.
         */
        if (Arr::hasAny($data, [
            'doctor_id',
            'appointment_date',
            'appointment_time',
        ])) {
            $doctorId = (int) (
                $data['doctor_id']
                ?? $appointment->doctor_id
            );

            $date = isset($data['appointment_date'])
                ? Carbon::parse($data['appointment_date'])->format('Y-m-d')
                : $appointment->appointment_date->format('Y-m-d');

            $time = $data['appointment_time']
                ?? substr($appointment->appointment_time, 0, 5);

            if (! Appointment::isAvailable(
                $doctorId,
                $date,
                $time,
                $appointment->id
            )) {
                return back()
                    ->withErrors([
                        'appointment_time' => 'The selected doctor is not available at this time.',
                    ])
                    ->withInput();
            }

            $data['appointment_date'] = Carbon::parse(
                "$date $time"
            );

            $data['appointment_time'] = $time;
        }

        $appointment->update($data);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Cancel appointment.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
       $this->authorize('cancel', $appointment);
        $appointment->update([
            'status' => Appointment::CANCELLED,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

public function calendar(Request $request): View
{
    $this->authorize('viewAny', Appointment::class);

    $user = $request->user();

    $month = (int) $request->input('month', now()->month);
    $year  = (int) $request->input('year', now()->year);

    $date = now()
        ->setYear($year)
        ->setMonth($month)
        ->startOfMonth();

    $query = Appointment::query()
        ->with([
            'patient.user',
            'doctor.user',
        ])
        ->whereYear('appointment_date', $year)
        ->whereMonth('appointment_date', $month);

    /*
     * Receptionists may only see appointments
     * created by themselves.
     */
    if ($user->isReceptionist()) {
        $receptionist = $user->receptionist;

        abort_unless($receptionist, 403);

        $query->where('receptionist_id', $receptionist->id);
    }

    if ($request->filled('doctor_id')) {
        $query->where('doctor_id', $request->integer('doctor_id'));
    }

    $appointments = $query
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->get()
        ->groupBy(function (Appointment $appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        });

    /*
     * Only needed for the receptionist calendar filter.
     */
    $doctors = Doctor::with('user')
        ->orderBy('id')
        ->get();

    return view('appointments.calendar', compact(
        'date',
        'appointments',
        'doctors'
    ));
}


}
