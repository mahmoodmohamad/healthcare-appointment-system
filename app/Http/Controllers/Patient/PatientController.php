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
	 public function __construct()
{
    // index→viewAny, create/store→create, show→view, edit/update→update
    $this->authorizeResource(Patient::class, 'patient');
}
    public function index(Request $request)
    {
        $query = Patient::with(['user', 'city', 'receptionist.user']);

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
    $cities = City::query()
        ->orderBy('name')
        ->get(['id', 'name']);

    return view('patients.create', [
        'cities' => $cities,
    ]);
}


    /**
     * Store a newly created patient
     */
   public function store(Request $request)
{
    $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

    $request->validate([
        'name'        => 'required|string|max:255',
        'email'       => 'required|email|max:255|unique:users,email',
        'password'    => ['required', 'confirmed', Password::min(10)],
        'national_id' => 'required|string|max:50|unique:patients,national_id',
        'phone'       => 'required|string|max:30',
        'city_id'     => 'required|exists:cities,id',
        'gender'      => ['nullable', Rule::in(['male', 'female'])],
        'birth_date'  => ['nullable', 'date', 'before:today'],
    ]);

    try {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]); // activation = DB default (true)

            Patient::create([
                'user_id'         => $user->id,
                'national_id'     => $request->national_id,
                'phone'           => $request->phone,
                'city_id'         => $request->city_id,
                'gender'          => $request->gender,
                'birth_date'      => $request->birth_date,
                'receptionist_id' => auth()->user()->receptionist?->id,
            ]);
        });

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully!');
    } catch (\Throwable $e) {
        report($e);
        return back()
            ->withErrors(['error' => 'Failed to register patient. Please try again.'])
            ->withInput($request->except('password', 'password_confirmation'));
    }
}


    /**
     * Display the specified patient
     */
   public function show(Patient $patient)
{
    $receptionistId = auth()->user()->receptionist->id;

    $patient->load(['user', 'city.country', 'receptionist.user']);

    // المواعيد اللي الـ Receptionist ده حجزها بس
    $own = fn () => $patient->appointments()->where('receptionist_id', $receptionistId);

    $stats = [
        'total_appointments'     => $own()->count(),
        'upcoming_appointments' => $own()->where('status', 'scheduled')
                                         ->whereDate('appointment_date', '>=', today())->count(),
        'completed_appointments'=> $own()->where('status', 'completed')->count(),
    ];

    $upcomingAppointments = $own()
        ->with('doctor.user')
        ->where('status', 'scheduled')
        ->whereDate('appointment_date', '>=', today())
        ->orderBy('appointment_date')->orderBy('appointment_time')
        ->get();

    return view('patients.show', compact('patient', 'stats', 'upcomingAppointments'));
}

    

    /**
     * Show the form for editing the patient
     */
 public function edit(Patient $patient)
{
    return view('patients.edit', [
        'patient' => $patient->load('user'),
        'cities'  => City::orderBy('name')->get(),
    ]);
}

public function update(Request $request, Patient $patient)
{
    $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

    $request->validate([
        'name'        => 'required|string|max:255',
        'email'       => 'required|email|max:255|unique:users,email,' . $patient->user_id,
        'national_id' => 'required|string|max:50|unique:patients,national_id,' . $patient->id,
        'phone'       => 'required|string|max:30',
        'city_id'     => 'required|exists:cities,id',
        'gender'      => ['nullable', Rule::in(['male', 'female'])],
        'birth_date'  => ['nullable', 'date', 'before:today'],
        'password'    => ['nullable', 'confirmed', Password::min(10)],
    ]);

    DB::transaction(function () use ($request, $patient) {
        $userData = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $patient->user->update($userData);

        $patient->update($request->only(
            'national_id', 'phone', 'city_id', 'gender', 'birth_date'
        ));
    });

    return redirect()->route('patients.show', $patient)
        ->with('success', 'Patient updated successfully!');
}
}