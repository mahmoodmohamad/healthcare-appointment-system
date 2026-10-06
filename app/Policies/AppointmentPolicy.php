<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->isReceptionist() || $user->isDoctor() || $user->isPatient();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isDoctor()) {
            return $appointment->doctor_id === $user->doctor?->id;
        }

        if ($user->isPatient()) {
            return $appointment->patient_id === $user->patient?->id;
        }

        return $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isReceptionist();
    }

    /**
     * Doctor: diagnosis / status on own, non-cancelled appointments.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isDoctor()
            && $appointment->doctor_id === $user->doctor?->id
            && $appointment->status !== 'cancelled';
    }

    /**
     * Reschedule / edit via API: receptionist (any) or the doctor who owns it.
     */
    public function manage(User $user, Appointment $appointment): bool
    {
        return $user->isReceptionist() || $this->update($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isReceptionist() && $appointment->status !== 'completed';
    }
}