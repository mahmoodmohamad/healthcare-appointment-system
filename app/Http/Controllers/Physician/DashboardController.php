<?php

namespace App\Http\Controllers\physician;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Physician Dashboard
     */
    public function dashboard()
    {
        $physician = auth()->user()->physician;

        // Today's statistics
        $stats = [
            'today_appointments' => $physician->appointments()
                ->whereDate('appointment_date', today())
                ->count(),
            
            'today_completed' => $physician->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'completed')
                ->count(),
            
            'today_pending' => $physician->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            
            'total_patients' => $physician->appointments()
                ->distinct('patient_id')
                ->count('patient_id'),
        ];

        // Today's appointments
        $todayAppointments = $physician->appointments()
            ->with(['patient.user', 'diagnosis'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        // Upcoming appointments (next 7 days)
        $upcomingAppointments = $physician->appointments()
            ->with(['patient.user'])
            ->where('appointment_date', '>', now())
            ->where('appointment_date', '<=', now()->addDays(7))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        return view('physician.dashboard', compact('stats', 'todayAppointments', 'upcomingAppointments'));
    }

}
