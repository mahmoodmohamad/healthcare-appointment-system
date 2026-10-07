<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Receptionist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    private const WITH = [
        'patient.user',
        'doctor.user',
        'receptionist.user',
        'diagnosis',
    ];

    /**
     * All appointments across every doctor, receptionist and clinic.
     * Admin is deliberately unfiltered — filters below are optional
     * query-string refinements, not scope restrictions.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $query = Appointment::query()
            ->with(self::WITH)
            ->latest('appointment_date')
            ->latest('appointment_time');

        $this->applyFilters($query, $request);

        $appointments = $query->paginate(20)->withQueryString();

        return view('admin.appointments.index', [
            'appointments'  => $appointments,
            'doctors'       => Doctor::with('user')->orderBy('id')->get(),
            'receptionists' => Receptionist::with('user')->orderBy('id')->get(),
            'filters'       => $request->only([
                'status', 'doctor_id', 'receptionist_id', 'date_from', 'date_to', 'search',
            ]),
        ]);
    }

    /**
     * Calendar view — same data, grouped by day for the month grid.
     */
    public function calendar(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        $date = now()->setYear($year)->setMonth($month)->startOfMonth();

        $query = Appointment::query()
            ->with(['patient.user', 'doctor.user', 'receptionist.user'])
            ->whereYear('appointment_date', $year)
            ->whereMonth('appointment_date', $month);

        $this->applyFilters($query, $request);

        $appointments = $query
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(fn (Appointment $a) => $a->appointment_date->format('Y-m-d'));

        return view('admin.appointments.calendar', [
            'date'         => $date,
            'appointments' => $appointments,
            'doctors'      => Doctor::with('user')->orderBy('id')->get(),
            'filters'      => $request->only(['doctor_id', 'receptionist_id', 'status']),
        ]);
    }

    /**
     * Read-only detail view for admin. No editing here — status changes
     * and diagnoses belong to the roles that own those actions.
     */
    public function show(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);

        $appointment->load(self::WITH);

        return view('admin.appointments.show', compact('appointment'));
    }

    // -------------------------------------------------------------------
    // Filters shared between index() and calendar()
    // -------------------------------------------------------------------
    private function applyFilters($query, Request $request): void
    {
        $query->when(
            $request->filled('status'),
            fn ($q) => $q->where('status', $request->string('status')->toString())
        );

        $query->when(
            $request->filled('doctor_id'),
            fn ($q) => $q->where('doctor_id', $request->integer('doctor_id'))
        );

        $query->when(
            $request->filled('receptionist_id'),
            fn ($q) => $q->where('receptionist_id', $request->integer('receptionist_id'))
        );

        // Date range (only for the list view; harmless on calendar too)
        $query->when(
            $request->filled('date_from'),
            fn ($q) => $q->whereDate('appointment_date', '>=', $request->date('date_from'))
        );

        $query->when(
            $request->filled('date_to'),
            fn ($q) => $q->whereDate('appointment_date', '<=', $request->date('date_to'))
        );

        // Free-text search across patient name, doctor name, national id
        $query->when(
            $request->filled('search'),
            function ($q) use ($request) {
                $term = trim((string) $request->input('search'));

                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('patient.user', function ($u) use ($term) {
                        $u->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('email', 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('patient', function ($p) use ($term) {
                        $p->where('national_id', 'LIKE', "%{$term}%")
                          ->orWhere('phone', 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('doctor.user', function ($u) use ($term) {
                        $u->where('name', 'LIKE', "%{$term}%");
                    });
                });
            }
        );
    }
}