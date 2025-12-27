<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
         \App\Models\Appointment::class => \App\Policies\AppointmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


        Auth::viaRequest('patient', function (Request $request) {
            return ($request->user() && $request->user()->isPatient()) ?
                $request->user() : null;
        });

        Auth::viaRequest('physicain', function (Request $request) {
            return ($request->user() && $request->user()->isPhysicain()) ?
                $request->user() : null;
        });
        Auth::viaRequest('radiologist', function (Request $request) {
            return ($request->user() && $request->user()->isRadiologist()) ?
                $request->user() : null;
        });
        Auth::viaRequest('LabSpecialist', function (Request $request) {
            return ($request->user() && $request->user()->isLabSpecialist()) ?
                $request->user() : null;
        });
        Auth::viaRequest('secretary', function (Request $request) {
            return ($request->user() && $request->user()->isSecretary()) ?
                $request->user() : null;
        });

        Auth::viaRequest('clinicManger', function (Request $request) {
            return ($request->user() && $request->user()->isClinicManger()) ?
                $request->user() : null;
        });

        Auth::viaRequest('admin', function (Request $request) {
            return ($request->user() && $request->user()->isAdmin()) ?
                $request->user() : null;
        });

        Gate::before(function ($user) {
            return $user->first_name === 'admin';
        });
    }
}
