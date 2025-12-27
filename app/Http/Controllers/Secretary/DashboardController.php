<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $secretary = Auth::user()->secretary;

        $totalPatients = $secretary->patients()->count();
        $totalAppointments = $secretary->appointments()->count();

        $todayAppointments = $secretary->appointments()
            ->whereDate('appointment_date', today())
            ->count();

        $upcomingAppointments = $secretary->appointments()
            ->whereDate('appointment_date', '>', today())
            ->count();

        return view('secretary.dashboard', compact(
            'totalPatients',
            'totalAppointments',
            'todayAppointments',
            'upcomingAppointments'
        ));
    }
}
