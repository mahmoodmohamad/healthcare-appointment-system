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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Relationships safe to load for appointment listings.
     *
     * Diagnosis is intentionally excluded here and loaded
     * only for users authorized to access medical information.
     */
    private const BASIC_WITH = [
        'patient.user',
        'doctor.user',
        'receptionist.user',
    ];

    /**
     * List appointments.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $user = $request->user();

        $query = Appointment::query()
            ->with(self::BASIC_WITH)
            ->latest('appointment_date')
            ->latest('appointment_time');

        $this->applyOwnershipFilter($query, $user);

        $data = $request->validate([
            'status' => [
                'sometimes',
                'nullable',
                'in:scheduled,completed,cancelled',
            ],
            'date' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (! empty($data['date'])) {
            $query->whereDate('appointment_date', $data['date']);
        }

        $appointments = $query
            ->paginate(15)
            ->withQueryString();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show create appointment form.
     */
    public function create(): View
    {
        $this->authorize('create', Appointment::class);

        $patients = Patient::query()
            ->whereHas('user', fn ($query) => $query->active())
            ->with('user')
            ->orderBy('id')
            ->get();

        $doctors = Doctor::query()
            ->whereHas('user', fn ($query) => $query->active())
            ->with('user')
            ->orderBy('id')
            ->get();

        return view('appointments.create', compact(
            'patients',
            'doctors'
        ));
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Appointment::class);

        $data = $request->validate([
            'patient_id' => [
                'required',
                'integer',
                'exists:patients,id',
            ],
            'doctor_id' => [
                'required',
                'integer',
                'exists:doctors,id',
            ],
            'appointment_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'appointment_time' => [
                'required',
                'date_format:H:i',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $patientIsActive = Patient::query()
            ->whereKey($data['patient_id'])
            ->whereHas('user', fn ($query) => $query->active())
            ->exists();
        $doctorIsActive = Doctor::query()
            ->whereKey($data['doctor_id'])
            ->whereHas('user', fn ($query) => $query->active())
            ->exists();

        if (! $patientIsActive || ! $doctorIsActive) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'doctor_id' => 'The selected doctor or patient account is inactive.',
            ]);
        }

        $user = $request->user();

        $receptionistId = null;

        if ($user->isReceptionist()) {
            $receptionistId = $user->receptionist?->id;

            abort_unless($receptionistId, 403);
        }

        /*
         * Do not accept receptionist_id or status from request.
         * Both values are controlled by the application.
         */
        try {
            Appointment::book([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'receptionist_id' => $receptionistId,
                'appointment_date' => Carbon::createFromFormat(
                    'Y-m-d',
                    $data['appointment_date']
                )->startOfDay(),
                'appointment_time' => $data['appointment_time'],
                'notes' => $data['notes'] ?? null,
                'status' => Appointment::SCHEDULED,
            ]);
        } catch (ValidationException $exception) {
            throw $exception;
        }

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Show appointment details.
     */
    public function show(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);

        $appointment->load(self::BASIC_WITH);

        /*
         * Only doctors and patients may load diagnosis here.
         * Receptionists must never receive diagnosis data.
         */
        $user = request()->user();

        if ($user->isDoctor() || $user->isPatient() || $user->isAdmin()) {
            $appointment->load('diagnosis');
        }

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

        $isReceptionist = $user->isReceptionist();
        $isDoctor = $user->isDoctor();

        /*
         * Validate only fields that this endpoint supports.
         * Patient and receptionist ownership cannot be reassigned.
         */
        $rules = [
            'doctor_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:doctors,id',
            ],
            'appointment_date' => [
                'sometimes',
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'appointment_time' => [
                'sometimes',
                'required',
                'date_format:H:i',
            ],
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],
        ];

        /*
         * Doctors may update status, but only to completed.
         * Receptionists cannot change status through this method.
         */
        if ($isDoctor) {
            $rules['status'] = [
                'sometimes',
                'required',
                'in:completed',
            ];
        }

        $data = $request->validate($rules);

        /*
         * Rescheduling is handled through the model method.
         * It checks slot availability and translates conflicts.
         */
        $rescheduleFields = [
            'doctor_id',
            'appointment_date',
            'appointment_time',
        ];

        if (Arr::hasAny($data, $rescheduleFields)) {
            if ($isDoctor) {
                abort(403, 'Doctors cannot reschedule appointments.');
            }

            $doctorId = (int) (
                $data['doctor_id'] ?? $appointment->doctor_id
            );

            $date = $data['appointment_date']
                ?? $appointment->appointment_date->format('Y-m-d');

            $time = $data['appointment_time']
                ?? substr($appointment->appointment_time, 0, 5);

            $doctorIsActive = Doctor::query()
                ->whereKey($doctorId)
                ->whereHas('user', fn ($query) => $query->active())
                ->exists();

            if (! $doctorIsActive) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'doctor_id' => 'The selected doctor account is inactive.',
                ]);
            }

            $appointment->reschedule(
                $doctorId,
                $date,
                $time
            );
        }

        /*
         * Do not update scheduling fields twice.
         */
        $remainingData = Arr::except(
            $data,
            $rescheduleFields
        );

        /*
         * Receptionists may update notes only.
         * Doctors may update notes and complete their appointment.
         */
        if ($isReceptionist) {
            $remainingData = Arr::only(
                $remainingData,
                ['notes']
            );
        }

        if (! empty($remainingData)) {
            if (isset($remainingData['status'])) {
                if (
                    $remainingData['status'] !== Appointment::COMPLETED
                    || ! $appointment->canTransitionTo(
                        Appointment::COMPLETED
                    )
                ) {
                    abort(403, 'This status transition is not allowed.');
                }
            }

            $appointment->update($remainingData);
        }

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Cancel appointment.
     */
    public function destroy(
        Appointment $appointment
    ): RedirectResponse {
        $this->authorize('cancel', $appointment);

        $appointment->cancel();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Display appointment calendar.
     */
    public function calendar(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $data = $request->validate([
            'month' => [
                'sometimes',
                'integer',
                'between:1,12',
            ],
            'year' => [
                'sometimes',
                'integer',
                'between:2000,2100',
            ],
            'doctor_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:doctors,id',
            ],
        ]);

        $month = (int) (
            $data['month'] ?? now()->month
        );

        $year = (int) (
            $data['year'] ?? now()->year
        );

        $date = Carbon::create(
            $year,
            $month,
            1
        )->startOfMonth();

        $user = $request->user();

        $query = Appointment::query()
            ->with(self::BASIC_WITH)
            ->whereYear('appointment_date', $year)
            ->whereMonth('appointment_date', $month);

        $this->applyOwnershipFilter($query, $user);

        if (! empty($data['doctor_id'])) {
            $query->where(
                'doctor_id',
                $data['doctor_id']
            );
        }

        $appointments = $query
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(
                fn (Appointment $appointment) =>
                    $appointment->appointment_date->format('Y-m-d')
            );

        $doctors = Doctor::query()
            ->with('user')
            ->orderBy('id')
            ->get();

        return view('appointments.calendar', compact(
            'date',
            'appointments',
            'doctors'
        ));
    }

    /**
     * Apply role-based appointment ownership filtering.
     */
    private function applyOwnershipFilter($query, $user): void
    {
        if ($user->isReceptionist()) {
            $receptionistId = $user->receptionist?->id;

            abort_unless($receptionistId, 403);

            $query->where(
                'receptionist_id',
                $receptionistId
            );

            return;
        }

        if ($user->isDoctor()) {
            $doctorId = $user->doctor?->id;

            abort_unless($doctorId, 403);

            $query->where(
                'doctor_id',
                $doctorId
            );

            return;
        }

        if ($user->isPatient()) {
            $patientId = $user->patient?->id;

            abort_unless($patientId, 403);

            $query->where(
                'patient_id',
                $patientId
            );
        }
    }
}
