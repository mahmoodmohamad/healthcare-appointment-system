<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Appointment, Physician, Patient};
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // List all appointments
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'physician.user', 'secretary.user']);

        // Filter by status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date = $request->date) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->latest('appointment_date')->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    // Show create form
    public function create()
    {
        $physicians = Physician::with('user')->get();
        $patients = Patient::with('user')->get();
        
        return view('appointments.create', compact('physicians', 'patients'));
    }

    // Store appointment
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'physician_id' => 'required|exists:physicians,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Check availability
        $available = Appointment::isAvailable(
            $request->physician_id,
            $request->appointment_date,
            $request->appointment_time
        );

        if (!$available) {
            return back()
                ->withErrors(['appointment_time' => 'This time slot is already booked.'])
                ->withInput();
        }

        // Combine date and time into appointment_date
        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);

        Appointment::create([
            'patient_id' => $request->patient_id,
            'physician_id' => $request->physician_id,
            'secretary_id' => auth()->user()->secretary->id ?? null,
            'appointment_date' => $appointmentDateTime,
            'appointment_time' => $request->appointment_time,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment booked successfully!');
    }

    // Show single appointment
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'physician.user', 'secretary.user', 'diagnosis']);
        
        return view('appointments.show', compact('appointment'));
    }

    // Cancel appointment
    public function destroy(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        
        return redirect()->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully!');
    }

    // ✅ Calendar view - Fixed method name
    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $date = Carbon::create($year, $month, 1);
        $startDate = $date->copy()->startOfMonth()->startOfWeek();
        $endDate = $date->copy()->endOfMonth()->endOfWeek();
        
        // Get appointments for this month
        $appointmentsQuery = Appointment::with(['patient.user', 'physician.user'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        // Filter by physician if requested
        if ($physicianId = $request->get('physician_id')) {
            $appointmentsQuery->where('physician_id', $physicianId);
        }

        $appointments = $appointmentsQuery->get()
            ->groupBy(function($appointment) {
                return $appointment->appointment_date->format('Y-m-d');
            });
        
        // Get physicians for filter
        $physicians = Physician::with('user')->get();
        
        return view('appointments.calendar', compact('appointments', 'date', 'physicians'));
    }
    
    // Get appointments by date (for AJAX)
    public function getAppointments(Request $request)
    {
        $date = $request->get('date');
        $physicianId = $request->get('physician_id');
        
        $query = Appointment::with(['patient.user', 'physician.user'])
            ->whereDate('appointment_date', $date);
        
        if ($physicianId) {
            $query->where('physician_id', $physicianId);
        }
        
        $appointments = $query->orderBy('appointment_time')->get();
        
        return response()->json($appointments);
    }

    // ✅ NEW: Get available time slots
    public function getAvailableSlots(Request $request)
    {
        $physicianId = $request->physician_id;
        $date = $request->date;

        // Define working hours (you can move this to config or database)
        $workingHours = [
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00'],
        ];

        // Get booked slots
        $bookedSlots = Appointment::where('physician_id', $physicianId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        // Generate available slots
        $availableSlots = [];
        foreach ($workingHours as $hours) {
            $current = Carbon::parse($hours['start']);
            $end = Carbon::parse($hours['end']);

            while ($current < $end) {
                $timeSlot = $current->format('H:i');
                if (!in_array($timeSlot, $bookedSlots)) {
                    $availableSlots[] = $timeSlot;
                }
                $current->addMinutes(30); // 30-minute slots
            }
        }

        return response()->json($availableSlots);
    }
}