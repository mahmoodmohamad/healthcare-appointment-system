<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Doctor::class);

        $query = Doctor::with(['user', 'city'])->withCount('appointments');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhere('specialization', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($cityId = $request->integer('city_id')) {
            $query->where('city_id', $cityId);
        }

        $doctors = $query->latest()->paginate(15)->withQueryString();

        return view('admin.doctors.index', [
            'doctors' => $doctors,
            'cities'  => City::orderBy('name')->get(),
            'filters' => $request->only(['search', 'city_id']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Doctor::class);

        return view('admin.doctors.create', [
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Doctor::class);

        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'confirmed', Password::min(10)],
            'specialization' => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:30'],
            'city_id'        => ['required', 'exists:cities,id'],
        ]);

        try {
            $doctor = DB::transaction(function () use ($data) {
                $user = new User([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                $user->forceFill(['activation' => true])->save();

                return Doctor::create([
                    'user_id'        => $user->id,
                    'specialization' => $data['specialization'],
                    'phone'          => $data['phone'],
                    'city_id'        => $data['city_id'],
                ]);
            });

            Log::info('admin.doctor.created', [
                'actor_id'  => auth()->id(),
                'doctor_id' => $doctor->id,
            ]);

            return redirect()
                ->route('admin.doctors.show', $doctor)
                ->with('success', 'Doctor account created successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors(['error' => 'Failed to create doctor. Please try again.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function show(Doctor $doctor): View
    {
        $this->authorize('view', $doctor);

        $doctor->load(['user', 'city.country']);

        $appointments = $doctor->appointments()
            ->with(['patient.user', 'diagnosis', 'receptionist.user'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(15);

        $stats = [
            'total_appointments' => $doctor->appointments()->count(),
            'completed'          => $doctor->appointments()->where('status', 'completed')->count(),
            'scheduled'          => $doctor->appointments()->where('status', 'scheduled')->count(),
            'cancelled'          => $doctor->appointments()->where('status', 'cancelled')->count(),
            'total_patients'     => $doctor->appointments()->distinct('patient_id')->count('patient_id'),
            'total_diagnoses'    => $doctor->diagnoses()->count(),
        ];

        return view('admin.doctors.show', compact('doctor', 'appointments', 'stats'));
    }
}