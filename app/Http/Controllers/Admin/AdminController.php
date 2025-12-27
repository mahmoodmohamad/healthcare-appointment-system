<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Patient, Physician, Secretary, Appointment, Diagnosis, City};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Admin Dashboard - Overview
     */
    public function dashboard()
    {
        // Overall Statistics
        $stats = [
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_physicians' => Physician::count(),
            'total_secretaries' => Secretary::count(),
            'total_appointments' => Appointment::count(),
            'total_diagnoses' => Diagnosis::count(),
            
            // Today
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            
            // This Month
            'month_appointments' => Appointment::whereMonth('created_at', now()->month)->count(),
            'month_patients' => Patient::whereMonth('created_at', now()->month)->count(),
            
            // Status breakdown
            'scheduled' => Appointment::where('status', 'scheduled')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        // Recent Activity
        $recentAppointments = Appointment::with(['patient.user', 'physician.user'])
            ->latest()
            ->take(10)
            ->get();

        $recentPatients = Patient::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Appointments by Status (for chart)
        $appointmentsByStatus = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly Appointments (last 6 months for chart)
        $monthlyData = Appointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('appointment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Physicians by Appointments
        $topPhysicians = Physician::withCount('appointments')
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
            'topPhysicians'
        ));
    }

    /**
     * System Statistics Page
     */
    public function statistics()
    {
        // Detailed statistics
        $stats = [
            'users_by_role' => [
                'patients' => Patient::count(),
                'physicians' => Physician::count(),
                'secretaries' => Secretary::count(),
                'admins' => User::admins()->count(),
            ],
            
            'appointments_by_month' => Appointment::select(
                    DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('appointment_date', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'appointments_by_physician' => Physician::withCount('appointments')
                ->with('user')
                ->having('appointments_count', '>', 0)
                ->orderByDesc('appointments_count')
                ->get(),
            
            'cities_distribution' => City::withCount(['patients', 'physicians', 'secretaries'])
                ->get(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}