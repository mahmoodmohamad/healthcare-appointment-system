<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\{Patient, User, City};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::with(['user', 'city', 'secretary.user']);

        // Search by name, national_id, or phone
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhere('national_id', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by city
        if ($cityId = $request->city_id) {
            $query->where('city_id', $cityId);
        }

        $patients = $query->latest()->paginate(15);
        $cities = City::all();

        return view('patients.index', compact('patients', 'cities'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        $cities = City::all();
        return view('patients.create', compact('cities'));
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'national_id' => 'required|string|unique:patients,national_id',
            'phone' => 'required|string',
            'city_id' => 'required|exists:cities,id',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activation' => true,
            ]);

            // Create patient profile
            $patient = Patient::create([
                'user_id' => $user->id,
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'city_id' => $request->city_id,
                'secretary_id' => auth()->user()->secretary->id ?? null,
            ]);

            DB::commit();

            return redirect()->route('patients.index')
                ->with('success', 'Patient registered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to register patient. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
{
    // Load basic patient relations
    $patient->load([
        'user',
        'city.country',
        'secretary.user',
        'appointments.physician.user',
        'appointments.diagnosis',
    ]);

    /**
     * =========================
     * 📊 Statistics
     * =========================
     */
    $stats = [
        'total_appointments' => $patient->appointments()->count(),

        'upcoming_appointments' => $patient->appointments()
            ->where('appointment_date', '>', now())
            ->where('status', 'scheduled')
            ->count(),

        'completed_appointments' => $patient->appointments()
            ->where('status', 'completed')
            ->count(),

        // via hasManyThrough (Patient -> Appointment -> Diagnosis)
        'total_diagnoses' => $patient->diagnoses()->count(),
    ];

    /**
     * =========================
     * 📅 Upcoming Appointments
     * =========================
     */
    $upcomingAppointments = $patient->appointments()
        ->with(['physician.user'])
        ->where('appointment_date', '>', now())
        ->where('status', 'scheduled')
        ->orderBy('appointment_date')
        ->get();

    /**
     * =========================
     * 🩺 Recent Diagnoses
     * =========================
     * Diagnosis -> Appointment -> Physician -> User
     */
    $recentDiagnoses = $patient->diagnoses()
        ->with(['appointment.physician.user'])
        ->latest()
        ->take(5)
        ->get();

    return view('patients.show', compact(
        'patient',
        'stats',
        'upcomingAppointments',
        'recentDiagnoses'
    ));
}

    /**
     * Show patient's complete medical history
     */
    public function medicalHistory(Patient $patient)
    {
        $patient->load(['user', 'city']);

        $appointments = $patient->appointments()
            ->with(['physician.user', 'diagnosis'])
            ->orderByDesc('appointment_date')
            ->paginate(10);

        return view('patients.medical-history', compact('patient', 'appointments'));
    }

    /**
     * Show the form for editing the patient
     */
   public function edit(Patient $patient)
{
    $cities = City::all();
    $secretaries = \App\Models\Secretary::with('user')->get(); // جلب كل السكرتير مع المستخدم

    return view('patients.edit', compact('patient', 'cities', 'secretaries'));
}


    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->user_id,
            'national_id' => 'required|string|unique:patients,national_id,' . $patient->id,
            'phone' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Update user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $patient->user()->update($userData);

            // Update patient
            $patient->update([
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'city_id' => $request->city_id,
            ]);

            DB::commit();

            return redirect()->route('patients.show', $patient)
                ->with('success', 'Patient updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update patient. Please try again.'])
                ->withInput();
        }
    }
}