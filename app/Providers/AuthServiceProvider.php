<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Admin bypass. Must return null (not false) for everyone else,
        // otherwise every policy check is denied.
        Gate::before(function ($user) {
            return $user->isAdmin() ? true : null;
        });
    }
}