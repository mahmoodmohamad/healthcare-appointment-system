<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Admin can view all appointments.
     * Other abilities are checked individually.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (
            $user->isAdmin()
            && in_array($ability, ['viewAny', 'view'], true)
        ) {
            return true;
        }

        return null;
    }

    /**
     * Who can access the appointments listing?
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isReceptionist()
            || $user->isDoctor()
            || $user->isPatient();
    }

    /**
     * View a specific appointment.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $this->ownsAsReceptionist($user, $appointment)
            || $this->ownsAsDoctor($user, $appointment)
            || $this->ownsAsPatient($user, $appointment);
    }

    /**
     * Create appointments.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    /**
     * Update appointment details.
     *
     * Receptionists can manage only appointments they created.
     * Doctors can manage appointments assigned to them.
     */
    public function manage(User $user, Appointment $appointment): bool
    {
        return $this->ownsAsReceptionist($user, $appointment)
            || $this->ownsAsDoctor($user, $appointment);
    }

    /**
     * Cancel an appointment.
     *
     * Receptionists can cancel their own scheduled appointments.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        return $appointment->status === Appointment::SCHEDULED
            && $this->ownsAsReceptionist($user, $appointment);
    }

    /**
     * Access diagnosis and medical information.
     *
     * Receptionists are deliberately excluded.
     */
    public function diagnose(User $user, Appointment $appointment): bool
    {
        return $appointment->status !== Appointment::CANCELLED
            && $this->ownsAsDoctor($user, $appointment);
    }

    /**
     * Reschedule an appointment.
     */
    public function reschedule(User $user, Appointment $appointment): bool
    {
        return $appointment->status === Appointment::SCHEDULED
            && (
                $this->ownsAsReceptionist($user, $appointment)
                || $this->ownsAsDoctor($user, $appointment)
            );
    }

    /**
     * Check receptionist ownership.
     */
    private function ownsAsReceptionist(
        User $user,
        Appointment $appointment
    ): bool {
        if (! $user->isReceptionist()) {
            return false;
        }

        $receptionistId = $user->receptionist?->id;

        return $receptionistId !== null
            && (int) $appointment->receptionist_id === (int) $receptionistId;
    }

    /**
     * Check doctor ownership.
     */
    private function ownsAsDoctor(
        User $user,
        Appointment $appointment
    ): bool {
        if (! $user->isDoctor()) {
            return false;
        }

        $doctorId = $user->doctor?->id;

        return $doctorId !== null
            && (int) $appointment->doctor_id === (int) $doctorId;
    }

    /**
     * Check patient ownership.
     */
    private function ownsAsPatient(
        User $user,
        Appointment $appointment
    ): bool {
        if (! $user->isPatient()) {
            return false;
        }

        $patientId = $user->patient?->id;

        return $patientId !== null
            && (int) $appointment->patient_id === (int) $patientId;
    }
}
