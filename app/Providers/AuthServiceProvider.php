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

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
         Doctor::class       => DoctorPolicy::class,
    Receptionist::class => ReceptionistPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

       
    }
}