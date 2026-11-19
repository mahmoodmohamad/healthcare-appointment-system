
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAppointmentRequest;
use App\Http\Requests\Api\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class AppointmentController extends Controller
{
    private const BASIC_WITH = [
        'patient.user',
        'doctor.user',
        'receptionist.user',
    ];

    public function index(Request $request)
    {
        $user = $request->user();

        if (! $this->canListAppointments($user)) {
            return response()->json([
                'message' => 'You are not authorized to view appointments.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['sometimes', 'string', 'in:scheduled,completed,cancelled'],
            'date' => ['sometimes', 'date_format:Y-m-d'],
        ]);

        $query = Appointment::query()
            ->with($this->relationsFor($user))
            ->latest('appointment_date')
            ->latest('appointment_time');

        $this->applyOwnershipFilter($query, $user);

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['date'])) {
            $query->whereDate('appointment_date', $validated['date']);
        }

        return AppointmentResource::collection(
            $query->paginate(15)->withQueryString()
        );
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $this->canManageAppointments($user)) {
            return response()->json([
                'message' => 'Only receptionists can book appointments.',
            ], 403);
        }

        $data = $request->validated();

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

        try {
            $appointment = Appointment::book([
                ...$data,
                'receptionist_id' => $user->isReceptionist()
                    ? $user->receptionist?->id
                    : null,
                'appointment_date' => Carbon::parse(
                    $data['appointment_date'] . ' ' . $data['appointment_time']
                ),
                'status' => 'scheduled',
            ]);
        } catch (QueryException $e) {
            throw Appointment::translateSlotConflict($e);
        }

        $appointment->load($this->relationsFor($user));

        return (new AppointmentResource($appointment))
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        Request $request,
        Appointment $appointment
    ): JsonResponse|AppointmentResource {
        $user = $request->user();

        if (! $this->canViewAppointment($user, $appointment)) {
            return response()->json([
                'message' => 'You are not authorized to view this appointment.',
            ], 403);
        }

        return new AppointmentResource(
            $appointment->load($this->relationsFor($user))
        );
    }

    public function update(
        UpdateAppointmentRequest $request,
        Appointment $appointment
    ): JsonResponse|AppointmentResource {
        $user = $request->user();

        if (! $this->canManageAppointment($user, $appointment)) {
            return response()->json([
                'message' => 'You are not authorized to update this appointment.',
            ], 403);
        }

        $data = $request->validated();

        $scheduleFields = [
            'doctor_id',
            'appointment_date',
            'appointment_time',
        ];

        if ($user->isDoctor() && Arr::hasAny($data, $scheduleFields)) {
            return response()->json([
                'message' => 'Doctors are not authorized to reschedule appointments.',
            ], 403);
        }

        if ($user->isReceptionist()) {
            // Receptionists may only update scheduling information.
            $allowed = [
                'doctor_id',
                'appointment_date',
                'appointment_time',
            ];

            $data = array_intersect_key(
                $data,
                array_flip($allowed)
            );
        } elseif ($user->isDoctor()) {
            // Doctors may update appointment status and notes,
            // but cannot change the booking or its ownership.
            $allowed = ['status', 'notes'];

            $data = array_intersect_key(
                $data,
                array_flip($allowed)
            );

            if (isset($data['status']) && $data['status'] === 'completed'
                && ! $appointment->diagnosis()->exists()) {
                return response()->json([
                    'message' => 'An appointment requires a diagnosis before completion.',
                ], 422);
            }

            if (isset($data['status']) && $data['status'] !== 'completed') {
                return response()->json([
                    'message' => 'Doctors may only mark appointments as completed.',
                ], 422);
            }
        }

        $scheduleChanged = Arr::hasAny($data, $scheduleFields);

        if ($scheduleChanged) {
            if (! $user->isAdmin() && ! $user->isReceptionist()) {
                return response()->json([
                    'message' => 'You are not authorized to reschedule this appointment.',
                ], 403);
            }

            if ($appointment->status !== 'scheduled') {
                return response()->json([
                    'message' => 'Only scheduled appointments can be rescheduled.',
                ], 422);
            }

            $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
            $doctorIsActive = Doctor::query()
                ->whereKey($doctorId)
                ->whereHas('user', fn ($query) => $query->active())
                ->exists();

            if (! $doctorIsActive) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'doctor_id' => 'The selected doctor account is inactive.',
                ]);
            }

            $date = $data['appointment_date']
                ?? $appointment->appointment_date->format('Y-m-d');

            $time = $data['appointment_time']
                ?? $appointment->appointment_time;

            $normalizedCurrentTime = substr((string) $appointment->appointment_time, 0, 5);
            $normalizedTime = substr((string) $time, 0, 5);
            $sameSlot = (int) $doctorId === (int) $appointment->doctor_id
                && $date === $appointment->appointment_date->format('Y-m-d')
                && $normalizedTime === $normalizedCurrentTime;

            if (! $sameSlot && ! Appointment::isAvailable($doctorId, $date, $normalizedTime, $appointment->id)) {
                return response()->json([
                    'message' => 'The selected doctor is not available at this time.',
                    'errors' => [
                        'appointment_time' => [
                            'This time slot is already booked.',
                        ],
                    ],
                ], 422);
            }

            try {
                $appointment->reschedule(
                    (int) $doctorId,
                    Carbon::parse($date)->toDateString(),
                    $normalizedTime
                );
            } catch (QueryException $e) {
                throw Appointment::translateSlotConflict($e);
            }

            unset(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time']
            );
        }

        if (! empty($data)) {
            $appointment->update($data);
        }

        $appointment->load($this->relationsFor($user));

        return new AppointmentResource($appointment);
    }

    public function destroy(
        Request $request,
        Appointment $appointment
    ): JsonResponse {
        $user = $request->user();

        if (! $this->canCancelAppointment($user, $appointment)) {
            return response()->json([
                'message' => 'You are not authorized to cancel this appointment.',
            ], 403);
        }

        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'message' => 'Only scheduled appointments can be cancelled.',
            ], 422);
        }

        $appointment->cancel();

        return response()->json([
            'message' => 'Appointment cancelled successfully.',
        ]);
    }

    private function canListAppointments($user): bool
    {
        return $user !== null && (
            $user->isAdmin()
            || $user->isReceptionist()
            || $user->isDoctor()
            || $user->isPatient()
        );
    }

    private function canManageAppointments($user): bool
    {
        return $user !== null
            && $user->isReceptionist()
            && $user->receptionist !== null;
    }

    private function canViewAppointment(
        $user,
        Appointment $appointment
    ): bool {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isReceptionist()) {
            return $this->isOwnAppointment($user, $appointment);
        }

        if ($user->isDoctor()) {
            return $user->doctor !== null
                && (int) $user->doctor->id === (int) $appointment->doctor_id;
        }

        if ($user->isPatient()) {
            return $user->patient !== null
                && (int) $user->patient->id === (int) $appointment->patient_id;
        }

        return false;
    }

    private function canManageAppointment(
        $user,
        Appointment $appointment
    ): bool {
        if ($user === null) {
            return false;
        }

        if ($user->isReceptionist()) {
            return $this->isOwnAppointment($user, $appointment);
        }

        return $user->isDoctor()
            && $user->doctor !== null
            && (int) $user->doctor->id === (int) $appointment->doctor_id;
    }

    private function canCancelAppointment(
        $user,
        Appointment $appointment
    ): bool {
        if ($user === null) {
            return false;
        }

        return $user->isReceptionist()
            && $this->isOwnAppointment($user, $appointment);
    }

    private function isOwnAppointment(
        $user,
        Appointment $appointment
    ): bool {
        return $user->receptionist !== null
            && (int) $appointment->receptionist_id === (int) $user->receptionist->id;
    }

    private function applyOwnershipFilter(
        Builder $query,
        $user
    ): void {
        if ($user->isReceptionist()) {
            $query->where(
                'receptionist_id',
                $user->receptionist->id
            );
        } elseif ($user->isDoctor()) {
            $query->where(
                'doctor_id',
                $user->doctor->id
            );
        } elseif ($user->isPatient()) {
            $query->where(
                'patient_id',
                $user->patient->id
            );
        }
    }

    private function relationsFor($user): array
    {
        $relations = self::BASIC_WITH;

        // Receptionists must never receive diagnosis data.
        if (! $user->isReceptionist()) {
            $relations[] = 'diagnosis';
        }

        return $relations;
    }
}
