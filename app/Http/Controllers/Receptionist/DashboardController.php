<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the receptionist dashboard.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $receptionist = $user->receptionist;

        abort_unless($receptionist, 403, 'Receptionist profile not found.');

        $today = today();

        $appointments = $receptionist->appointments();

        $patients = $receptionist->patients();

        // Dashboard statistics
        $totalPatients = (clone $patients)->count();

        $totalAppointments = (clone $appointments)->count();

        $todayBookings = (clone $appointments)
            ->whereDate('created_at', $today)
            ->count();

        $todayAppointments = (clone $appointments)
            ->whereDate('appointment_date', $today)
            ->where('status', 'scheduled')
            ->count();

        $upcomingAppointments = (clone $appointments)
            ->whereDate('appointment_date', '>=', $today)
            ->where('status', 'scheduled')
            ->count();

        $completedAppointments = (clone $appointments)
            ->where('status', 'completed')
            ->count();

        // Recent patients registered by this receptionist
        $recentPatients = (clone $patients)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming appointments created by this receptionist only
        $upcomingAppointmentsList = (clone $appointments)
            ->with([
                'patient.user',
                'doctor.user',
            ])
            ->whereDate('appointment_date', '>=', $today)
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(6)
            ->get();

        // Prepare display-ready appointment information
        $upcomingAppointmentsList->each(function ($appointment) {
            $appointment->date_label = $appointment->appointment_date
                ? $appointment->appointment_date->format('M d, Y')
                : 'Date not set';

            $time = $appointment->appointment_time
                ?? $appointment->appointment_date?->format('H:i');

            $appointment->time_label = $time
                ? \Illuminate\Support\Carbon::parse($time)->format('h:i A')
                : 'Time not set';

            $appointment->patient_name = $appointment->patient?->user?->name
                ?? 'Unknown patient';

            $appointment->doctor_name = $appointment->doctor?->user?->name
                ?? 'Unknown doctor';
        });

        // Prepare recent patient display information
        $recentPatients->each(function ($patient) {
            $patient->display_name = $patient->user?->name
                ?? 'Unknown patient';

            $patient->registered_label = $patient->created_at
                ? $patient->created_at->diffForHumans()
                : 'Registration date unavailable';

            $patient->initials = collect(
                preg_split('/\s+/', trim($patient->display_name))
            )
                ->filter()
                ->take(2)
                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                ->implode('');
        });

        $greeting = now()->hour < 12
            ? 'Good morning'
            : (now()->hour < 18 ? 'Good afternoon' : 'Good evening');

        return view('receptionist.dashboard', [
            'receptionistName' => $user->name,
            'greeting' => $greeting,

            'totalPatients' => $totalPatients,
            'totalAppointments' => $totalAppointments,
            'todayBookings' => $todayBookings,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'completedAppointments' => $completedAppointments,

            'recentPatients' => $recentPatients,
            'upcomingAppointmentsList' => $upcomingAppointmentsList,
        ]);
    }
}
