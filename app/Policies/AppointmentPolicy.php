<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Admin can *see* everything — nothing more.
     * Write actions still go through the methods below.
     */
    public function before(User $user, string $ability)
    {
        return ($user->isAdmin() && in_array($ability, ['viewAny', 'view'], true))
            ? true
            : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isReceptionist()
            || $user->isDoctor()
            || $user->isPatient();
    }

    public function view(User $user, Appointment $a): bool
    {
        return $user->isReceptionist()
            || $this->ownsAsDoctor($user, $a)
            || $this->ownsAsPatient($user, $a);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    public function manage(User $user, Appointment $a): bool
    {
        return $user->isReceptionist() || $this->ownsAsDoctor($user, $a);
    }

    public function cancel(User $user, Appointment $a): bool
    {
        return $user->isReceptionist() && $a->status === Appointment::SCHEDULED;
    }

    public function diagnose(User $user, Appointment $a): bool
    {
        return $this->ownsAsDoctor($user, $a)
            && $a->status !== Appointment::CANCELLED;
    }

    public function reschedule(User $user, Appointment $a): bool
    {
        return $a->status === Appointment::SCHEDULED
            && ($user->isReceptionist() || $this->ownsAsDoctor($user, $a));
    }

    // ---- helpers -------------------------------------------------------
    private function ownsAsDoctor(User $user, Appointment $a): bool
    {
        $id = $user->doctor?->id;
        return $user->isDoctor() && $id !== null && (int) $a->doctor_id === (int) $id;
    }

    private function ownsAsPatient(User $user, Appointment $a): bool
    {
        $id = $user->patient?->id;
        return $user->isPatient() && $id !== null && (int) $a->patient_id === (int) $id;
    }
}