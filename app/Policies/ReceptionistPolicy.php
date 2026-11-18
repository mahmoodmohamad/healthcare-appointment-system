<?php

namespace App\Policies;

use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceptionistPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        return ($user->isAdmin() && in_array($ability, ['viewAny', 'view', 'create'], true))
            ? true
            : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Receptionist $receptionist): bool
    {
        // ReceptionistPolicy::view
        return $user->isAdmin()
            || ($user->isReceptionist()
                && (int) $user->receptionist?->id === (int) $receptionist->id);

        // DoctorPolicy::view
        return $user->isAdmin()
            || ($user->isDoctor() && (int) $user->doctor?->id === (int) $doctor->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Receptionist $receptionist): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Receptionist $receptionist): bool
    {
        return $user->isAdmin();
    }
}
