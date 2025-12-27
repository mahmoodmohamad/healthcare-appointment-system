<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LabSpecialist\DashboardController as LabSpecialistDashboardController;
use App\Http\Controllers\Radiologist\DashboardController as RadiologistDashboardController;
use App\Models\XraySpecialist;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Container $app)
    {
        return $app->call($app->make($this->getClassName($request)));
    }

    public function getClassName(Request $request)
    {
        //if ($request->user()->isPatient())
          //  return Patient\DashboardController::class;

        if ($request->user()->isPhysicain())
            return Physician\DashboardController::class;

        if ($request->user()->isRadiologist())
            return RadiologistDashboardController::class;
        
        if ($request->user()->isLabSpecialist())
            return LabSpecialistDashboardController::class;
        
        if ($request->user()->isSecretary())
            return Secretary\DashboardController::class;

        if ($request->user()->isClinicManger())
            return ClinicManger\DashboardController::class;

        if ($request->user()->isAdmin())
            return Admin\DashboardController::class;
    }
}
