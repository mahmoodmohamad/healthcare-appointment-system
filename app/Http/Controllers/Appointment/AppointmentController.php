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
use Illuminate\Support\Arr;

/**
 * All authorization goes through AppointmentPolicy
 * (same rules as the web routes).
 */
class AppointmentController extends Controller
{
    private const WITH = ['patient.user', 'doctor.user', 'receptionist.user', 'diagnosis'];

    public function index(Request $request)
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

        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()));
        $query->when($request->filled('date'), fn ($q) => $q->whereDate('appointment_date', $request->date('date')));

        return AppointmentResource::collection($query->paginate(15));
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $this->authorize('create', Appointment::class);

        $data = $request->validated();

        if (! Appointment::isAvailable($data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
            return $this->slotTaken();
        }

        $appointment = Appointment::create([
            ...$data,
            'receptionist_id'  => optional($request->user()->receptionist)->id,
            'appointment_date' => Carbon::parse($data['appointment_date'] . ' ' . $data['appointment_time']),
            'status'           => 'scheduled',
        ]);

        return (new AppointmentResource($appointment->load(self::WITH)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Appointment $appointment): AppointmentResource
    {
        $this->authorize('view', $appointment);

        return new AppointmentResource($appointment->load(self::WITH));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse|AppointmentResource
    {
        $this->authorize('manage', $appointment);

        $user = $request->user();
        $data = $request->validated();

        // A doctor may only change status / notes, never reassign or reschedule
        if (! ($user->isAdmin() || $user->isReceptionist())) {
            $data = Arr::only($data, ['status', 'notes']);
        }

        if (Arr::hasAny($data, ['doctor_id', 'appointment_date', 'appointment_time'])) {
            $doctorId = (int) ($data['doctor_id'] ?? $appointment->doctor_id);
            $date     = isset($data['appointment_date'])
                ? Carbon::parse($data['appointment_date'])->format('Y-m-d')
                : $appointment->appointment_date->format('Y-m-d');
            $time     = $data['appointment_time'] ?? substr($appointment->appointment_time, 0, 5);

            if (! Appointment::isAvailable($doctorId, $date, $time, $appointment->id)) {
                return $this->slotTaken();
            }

            $data['appointment_date'] = Carbon::parse("$date $time");
            $data['appointment_time'] = $time;
        }

        $appointment->update($data);

        return new AppointmentResource($appointment->load(self::WITH));
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $this->authorize('delete', $appointment);

        $appointment->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Appointment cancelled successfully.']);
    }

    private function slotTaken(): JsonResponse
    {
        return response()->json([
            'message' => 'The selected doctor is not available at this time.',
            'errors'  => ['appointment_time' => ['This time slot is already booked.']],
        ], 422);
    }
}