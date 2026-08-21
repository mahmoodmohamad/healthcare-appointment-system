<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAppointmentRequest;
use App\Http\Requests\Api\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $this->canListAppointments($user)) {
            return response()->json([
                'message' => 'You are not authorized to view appointments.',
            ], 403);
        }

        $query = Appointment::query()
            ->with(['patient.user', 'physician.user', 'secretary.user', 'diagnosis'])
            ->latest('appointment_date')
            ->latest('appointment_time');

        if ($user->isPhysician()) {
            $query->where('physician_id', $user->physician->id);
        } elseif ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        }

        $query->when($request->filled('status'), function ($builder) use ($request) {
            $builder->where('status', $request->string('status')->toString());
        });

        $query->when($request->filled('date'), function ($builder) use ($request) {
            $builder->whereDate('appointment_date', $request->date('date'));
        });

        return AppointmentResource::collection($query->paginate(15));
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $this->canManageAppointments($user)) {
            return response()->json([
                'message' => 'Only administrators and secretaries can book appointments.',
            ], 403);
        }

        $data = $request->validated();

        if (! Appointment::isAvailable(
            $data['physician_id'],
            $data['appointment_date'],
            $data['appointment_time']
        )) {
            return response()->json([
                'message' => 'The selected physician is not available at this time.',
                'errors' => [
                    'appointment_time' => ['This time slot is already booked.'],
                ],
            ], 422);
        }

        $appointment = Appointment::create([
            ...$data,
            'secretary_id' => optional($user->secretary)->id,
            'appointment_date' => Carbon::parse(
                $data['appointment_date'] . ' ' . $data['appointment_time']
            ),
            'status' => 'scheduled',
        ]);

        $appointment->load(['patient.user', 'physician.user', 'secretary.user', 'diagnosis']);

        return (new AppointmentResource($appointment))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse|AppointmentResource
    {
        if (! $this->canViewAppointment($request->user(), $appointment)) {
            return response()->json([
                'message' => 'You are not authorized to view this appointment.',
            ], 403);
        }

        return new AppointmentResource(
            $appointment->load(['patient.user', 'physician.user', 'secretary.user', 'diagnosis'])
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
        $physicianId = $data['physician_id'] ?? $appointment->physician_id;
        $date = $data['appointment_date'] ?? $appointment->appointment_date->format('Y-m-d');
        $time = $data['appointment_time'] ?? $appointment->appointment_time;

        if (isset($data['appointment_date']) || isset($data['appointment_time']) || isset($data['physician_id'])) {
            $isSameSlot = $physicianId === $appointment->physician_id
                && $date === $appointment->appointment_date->format('Y-m-d')
                && $time === $appointment->appointment_time;

            if (! $isSameSlot && ! Appointment::isAvailable($physicianId, $date, $time)) {
                return response()->json([
                    'message' => 'The selected physician is not available at this time.',
                    'errors' => [
                        'appointment_time' => ['This time slot is already booked.'],
                    ],
                ], 422);
            }

            $data['appointment_date'] = Carbon::parse($date . ' ' . $time);
            $data['appointment_time'] = $time;
        }

        $appointment->update($data);
        $appointment->load(['patient.user', 'physician.user', 'secretary.user', 'diagnosis']);

        return new AppointmentResource($appointment);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        if (! $this->canManageAppointment($request->user(), $appointment)) {
            return response()->json([
                'message' => 'You are not authorized to cancel this appointment.',
            ], 403);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Appointment cancelled successfully.',
        ]);
    }

    private function canListAppointments($user): bool
    {
        return $user !== null && (
            $user->isAdmin()
            || $user->isSecretary()
            || $user->isPhysician()
            || $user->isPatient()
        );
    }

    private function canManageAppointments($user): bool
    {
        return $user !== null && ($user->isAdmin() || $user->isSecretary());
    }

    private function canViewAppointment($user, Appointment $appointment): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $user->isSecretary()) {
            return true;
        }

        return ($user->isPhysician() && optional($user->physician)->id === $appointment->physician_id)
            || ($user->isPatient() && optional($user->patient)->id === $appointment->patient_id);
    }

    private function canManageAppointment($user, Appointment $appointment): bool
    {
        if ($this->canManageAppointments($user)) {
            return true;
        }

        return $user !== null
            && $user->isPhysician()
            && optional($user->physician)->id === $appointment->physician_id;
    }
}
