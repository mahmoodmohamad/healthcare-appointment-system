<?php

namespace App\Http\Controllers\Doctor;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Doctor Dashboard
     */
    public function dashboard()
    {
        $doctor = auth()->user()->doctor;

        // Today's statistics
        $stats = [
            'today_appointments' => $doctor->appointments()
                ->whereDate('appointment_date', today())
                ->count(),
            
            'today_completed' => $doctor->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'completed')
                ->count(),
            
            'today_pending' => $doctor->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            
            'total_patients' => $doctor->appointments()
                ->distinct('patient_id')
                ->count('patient_id'),
        ];

        // Today's appointments
        $todayAppointments = $doctor->appointments()
            ->with(['patient.user', 'diagnosis'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        // Upcoming appointments (next 7 days)
        $upcomingAppointments = $doctor->appointments()
            ->with(['patient.user'])
            ->where('appointment_date', '>', now())
            ->where('appointment_date', '<=', now()->addDays(7))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        return view('doctor.dashboard', compact('stats', 'todayAppointments', 'upcomingAppointments'));
    }

}
