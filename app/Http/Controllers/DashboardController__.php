<?php

namespace App\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Auth ;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Container $app)
    {
		if (Auth::check()) {
        return $app->call($app->make($this->getClassName($request)));
		}
    }

    public function getClassName(Request $request)
    {
        //if ($request->user()->isPatient())
          //  return Patient\DashboardController::class;

        if ($request->user()->isPhysicain())
            return Physician\DashboardController::class;

        if ($request->user()->isSecretary())
            return Secretary\DashboardController::class;

        if ($request->user()->isClinicManger())
            return ClinicManger\DashboardController::class;

        if ($request->user()->isAdmin())
            return Admin\DashboardController::class;
    }
}
