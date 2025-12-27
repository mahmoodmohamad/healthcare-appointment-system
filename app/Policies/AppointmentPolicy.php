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
        return $user->hasRole('secretary')
            || $user->hasRole('physician');
    }

    /**
     * View a single appointment
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Physician can view his own appointments
        if ($user->hasRole('physician')) {
            return $appointment->physician_id === $user->physician?->id;
        }

        // Secretary can view appointments she created
        if ($user->hasRole('secretary')) {
            return $appointment->secretary_id === $user->secretary?->id;
        }

        return false;
    }

    /**
     * Create appointment (secretary only)
     */
    public function create(User $user): bool
    {
        return $user->hasRole('secretary');
    }

    /**
     * Update appointment
     * (Physician updates diagnosis / status)
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('physician')
            && $appointment->physician_id === $user->physician?->id
            && $appointment->status !== 'cancelled';
    }

    /**
     * Delete appointment (secretary only)
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('secretary')
            && $appointment->secretary_id === $user->secretary?->id
            && $appointment->status !== 'completed';
    }
}
