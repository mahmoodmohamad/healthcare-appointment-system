<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $patient = Auth::user()->patient()->with('user')->firstOrFail();
        $appointments = $patient->appointments()
            ->with(['physician.user', 'diagnosis'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->get();

        $upcomingAppointments = $appointments
            ->where('status', 'scheduled')
            ->filter(fn ($appointment) => $appointment->full_date_time >= now())
            ->sortBy('full_date_time')
            ->take(5);

        $completedAppointments = $appointments->where('status', 'completed')->count();
        $scheduledAppointments = $appointments->where('status', 'scheduled')->count();
        $cancelledAppointments = $appointments->where('status', 'cancelled')->count();
        $latestDiagnosis = $appointments
            ->whereNotNull('diagnosis')
            ->sortByDesc('full_date_time')
            ->first()?->diagnosis;

        return view('patient.dashboard', compact(
            'patient',
            'appointments',
            'upcomingAppointments',
            'completedAppointments',
            'scheduledAppointments',
            'cancelledAppointments',
            'latestDiagnosis'
        ));
    }
}
