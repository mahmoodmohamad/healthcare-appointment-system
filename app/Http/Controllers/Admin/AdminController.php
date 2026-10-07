<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Patient, Doctor, Receptionist, Appointment, Diagnosis, City};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Admin Dashboard — system overview.
     */
    public function dashboard()
    {
        $stats = [
            // Totals
            'total_users'          => User::count(),
            'total_patients'       => Patient::count(),
            'total_doctors'        => Doctor::count(),
            'total_receptionists'  => Receptionist::count(),
            'total_appointments'   => Appointment::count(),
            'total_diagnoses'      => Diagnosis::count(),

            // Today
            'today_appointments'   => Appointment::whereDate('appointment_date', today())->count(),

            // This month
            'month_appointments'   => Appointment::whereMonth('created_at', now()->month)
                                                ->whereYear('created_at', now()->year)
                                                ->count(),
            'month_patients'       => Patient::whereMonth('created_at', now()->month)
                                                ->whereYear('created_at', now()->year)
                                                ->count(),

            // Status breakdown
            'scheduled'            => Appointment::where('status', 'scheduled')->count(),
            'completed'            => Appointment::where('status', 'completed')->count(),
            'cancelled'            => Appointment::where('status', 'cancelled')->count(),
        ];

        // Recent activity
        $recentAppointments = Appointment::with(['patient.user', 'doctor.user'])
            ->latest('created_at')
            ->take(8)
            ->get();

        $recentPatients = Patient::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Chart data
        $appointmentsByStatus = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $monthlyData = Appointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('appointment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topDoctors = Doctor::withCount('appointments')
            ->with('user')
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        // New: top receptionists by bookings
        $topReceptionists = Receptionist::withCount('appointments')
            ->with('user')
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentAppointments',
            'recentPatients',
            'appointmentsByStatus',
            'monthlyData',
            'topDoctors',
            'topReceptionists'
        ));
    }

    /**
     * Detailed statistics page.
     */
    public function statistics()
    {
        $stats = [
            'users_by_role' => [
                'patients'      => Patient::count(),
                'doctors'       => Doctor::count(),
                'receptionists' => Receptionist::count(),
                'admins'        => User::admins()->count(),
            ],

            'appointments_by_month' => Appointment::select(
                    DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('appointment_date', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get(),

            'appointments_by_doctor' => Doctor::withCount('appointments')
                ->with('user')
                ->having('appointments_count', '>', 0)
                ->orderByDesc('appointments_count')
                ->get(),

            'cities_distribution' => City::withCount(['patients', 'doctors', 'receptionists'])
                ->get(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}