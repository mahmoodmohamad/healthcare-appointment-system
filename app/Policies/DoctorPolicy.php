<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoctorPolicy
{
    use HandlesAuthorization;

    /**
     * Admin sees every doctor; everyone else sees doctors they're
     * already linked to (a doctor sees themselves; a patient sees
     * doctors they have appointments with — but for the admin panel
     * only admin reaches these views).
     */
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

    public function view(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin()
            || ($user->isDoctor() && $user->doctor?->id === $doctor->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin();
    }
}