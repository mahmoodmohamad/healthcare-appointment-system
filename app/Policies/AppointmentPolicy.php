<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Admin bypass (optional but recommended)
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * View list of appointments
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('receptionist')
            || $user->hasRole('doctor');
    }

    /**
     * View a single appointment
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Doctor can view his own appointments
        if ($user->hasRole('doctor')) {
            return $appointment->doctor_id === $user->doctor?->id;
        }

        // Receptionist can view appointments she created
        if ($user->hasRole('receptionist')) {
            return $appointment->receptionist_id === $user->receptionist?->id;
        }

        return false;
    }

    /**
     * Create appointment (receptionist only)
     */
    public function create(User $user): bool
    {
        return $user->hasRole('receptionist');
    }

    /**
     * Update appointment
     * (Doctor updates diagnosis / status)
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('doctor')
            && $appointment->doctor_id === $user->doctor?->id
            && $appointment->status !== 'cancelled';
    }

    /**
     * Delete appointment (receptionist only)
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('receptionist')
            && $appointment->receptionist_id === $user->receptionist?->id
            && $appointment->status !== 'completed';
    }
}
