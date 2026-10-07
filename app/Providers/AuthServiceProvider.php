<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Receptionist;
use App\Policies\AppointmentPolicy;
use App\Policies\DoctorPolicy;
use App\Policies\ReceptionistPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Patient;
use App\Policies\PatientPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
         Doctor::class       => DoctorPolicy::class,
    Receptionist::class => ReceptionistPolicy::class,
      Patient::class      => PatientPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

       
    }
}