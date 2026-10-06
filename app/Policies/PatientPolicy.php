<?php

namespace App\Policies;

use App\Models\{Patient, User};

class PatientPolicy
{
    public function view(User $user, Patient $patient): bool
    {
        // Doctor: only patients they have an appointment with
        if ($user->doctor) {
            return $patient->appointments()
                ->where('doctor_id', $user->doctor->id)
                ->exists();
        }

        return true; // admin / receptionist
    }

    public function update(User $user, Patient $patient): bool
    {
        return ! $user->doctor; // doctors can't edit patient accounts
    }
}