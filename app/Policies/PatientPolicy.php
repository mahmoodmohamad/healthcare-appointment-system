<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * Admin can view any patient record. Admin is intentionally NOT
     * allowed to create/update/delete from this policy — those actions
     * belong to receptionists (or explicit admin user-management paths).
     */
    public function before(User $user, string $ability)
    {
        return ($user->isAdmin() && in_array($ability, ['viewAny', 'view'], true))
            ? true
            : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    public function view(User $user, Patient $patient): bool
    {
        // Admin handled in before().
        if ($user->isReceptionist()) {
            return true;
        }

        if ($user->isDoctor()) {
            $doctorId = $user->doctor?->id;
            return $doctorId !== null
                && $patient->appointments()
                    ->where('doctor_id', $doctorId)
                    ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isReceptionist();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isReceptionist();
    }

    public function delete(User $user, Patient $patient): bool
    {
        // Never allow patient hard-delete; deactivate the user instead.
        return false;
    }
}