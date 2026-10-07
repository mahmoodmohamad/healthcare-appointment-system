<?php
namespace Tests\Concerns;

use App\Models\{Admin, Doctor, Patient, Receptionist, User};

trait CreatesRoles
{
    protected function adminUser(): User
    {
        $u = User::factory()->create();
        Admin::create(['user_id' => $u->id]);
        return $u;
    }
    protected function doctorUser(): User       { return Doctor::factory()->create()->user; }
    protected function receptionistUser(): User { return Receptionist::factory()->create()->user; }
    protected function patientUser(): User      { return Patient::factory()->create()->user; }
}