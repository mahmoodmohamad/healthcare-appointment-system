<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $receptionist = Auth::user()->receptionist;

        $totalPatients = $receptionist->patients()->count();
        $totalAppointments = $receptionist->appointments()->count();

        $todayAppointments = $receptionist->appointments()
            ->whereDate('appointment_date', today())
            ->count();

        $upcomingAppointments = $receptionist->appointments()
            ->whereDate('appointment_date', '>', today())
            ->count();

        return view('receptionist.dashboard', compact(
            'totalPatients',
            'totalAppointments',
            'todayAppointments',
            'upcomingAppointments'
        ));
    }
}
