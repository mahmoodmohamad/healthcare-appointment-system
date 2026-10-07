<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminPatientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Patient::class);

        $query = Patient::with(['user', 'city', 'receptionist.user'])
            ->withCount('appointments');

        // --- Filters ---------------------------------------------------
        if ($search = trim((string) $request->query('search', ''))) {
            $query->search($search);
        }

        if ($cityId = $request->integer('city_id')) {
            $query->where('city_id', $cityId);
        }

        if ($receptionistId = $request->integer('receptionist_id')) {
            $query->where('receptionist_id', $receptionistId);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->string('gender')->toString());
        }

        $patients = $query->latest()->paginate(20)->withQueryString();

        return view('admin.patients.index', [
            'patients'       => $patients,
            'cities'         => City::orderBy('name')->get(),
            'receptionists'  => Receptionist::with('user')->orderBy('id')->get(),
            'filters'        => $request->only([
                'search', 'city_id', 'receptionist_id', 'gender',
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Patient::class);

        return view('admin.patients.create', [
            'cities'         => City::orderBy('name')->get(),
            'receptionists'  => Receptionist::with('user')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required', 'confirmed', Password::min(10)],
            'national_id'     => ['required', 'string', 'max:50', 'unique:patients,national_id'],
            'phone'           => ['required', 'string', 'max:30'],
            'gender'          => ['nullable', Rule::in(['male', 'female'])],
            'birth_date'      => ['nullable', 'date', 'before:today'],
            'city_id'         => ['required', 'exists:cities,id'],
            'receptionist_id' => ['nullable', 'exists:receptionists,id'],
        ]);

        try {
            $patient = DB::transaction(function () use ($data) {
                $user = new User([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                $user->forceFill(['activation' => true])->save();

                return Patient::create([
                    'user_id'         => $user->id,
                    'national_id'     => $data['national_id'],
                    'phone'           => $data['phone'],
                    'gender'          => $data['gender'] ?? null,
                    'birth_date'      => $data['birth_date'] ?? null,
                    'city_id'         => $data['city_id'],
                    'receptionist_id' => $data['receptionist_id'] ?? null,
                ]);
            });

            Log::info('admin.patient.created', [
                'actor_id'   => auth()->id(),
                'patient_id' => $patient->id,
            ]);

            return redirect()
                ->route('admin.patients.show', $patient)
                ->with('success', 'Patient registered successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors(['error' => 'Failed to register patient. Please try again.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function show(Patient $patient): View
    {
        $this->authorize('view', $patient);

        $patient->load([
            'user',
            'city.country',
            'receptionist.user',
            'appointments.doctor.user',
            'appointments.diagnosis',
        ]);

        $stats = [
            'total_appointments'     => $patient->appointments()->count(),
            'scheduled'              => $patient->appointments()->where('status', 'scheduled')->count(),
            'completed'              => $patient->appointments()->where('status', 'completed')->count(),
            'cancelled'              => $patient->appointments()->where('status', 'cancelled')->count(),
            'total_diagnoses'        => $patient->diagnoses()->count(),
            'distinct_doctors'       => $patient->appointments()
                                            ->distinct('doctor_id')
                                            ->count('doctor_id'),
        ];

        $appointments = $patient->appointments()
            ->with(['doctor.user', 'diagnosis'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(15);

        return view('admin.patients.show', compact('patient', 'stats', 'appointments'));
    }
}