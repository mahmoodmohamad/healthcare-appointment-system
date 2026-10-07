<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminReceptionistController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Receptionist::class);

        $query = Receptionist::with(['user', 'city'])
            ->withCount(['appointments', 'patients']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($cityId = $request->integer('city_id')) {
            $query->where('city_id', $cityId);
        }

        $receptionists = $query->latest()->paginate(15)->withQueryString();

        return view('admin.receptionists.index', [
            'receptionists' => $receptionists,
            'cities'        => City::orderBy('name')->get(),
            'filters'       => $request->only(['search', 'city_id']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Receptionist::class);

        return view('admin.receptionists.create', [
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Receptionist::class);

        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)],
            'phone'    => ['required', 'string', 'max:30'],
            'city_id'  => ['required', 'exists:cities,id'],
        ]);

        try {
            $receptionist = DB::transaction(function () use ($data) {
                $user = new User([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                $user->forceFill(['activation' => true])->save();

                return Receptionist::create([
                    'user_id' => $user->id,
                    'phone'   => $data['phone'],
                    'city_id' => $data['city_id'],
                ]);
            });

            Log::info('admin.receptionist.created', [
                'actor_id'        => auth()->id(),
                'receptionist_id' => $receptionist->id,
            ]);

            return redirect()
                ->route('admin.receptionists.show', $receptionist)
                ->with('success', 'Receptionist account created successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors(['error' => 'Failed to create receptionist. Please try again.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function show(Receptionist $receptionist): View
    {
        $this->authorize('view', $receptionist);

        $receptionist->load(['user', 'city.country']);

        $appointments = $receptionist->appointments()
            ->with(['patient.user', 'doctor.user', 'diagnosis'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(15);

        $stats = [
            'total_appointments' => $receptionist->appointments()->count(),
            'scheduled'          => $receptionist->appointments()->where('status', 'scheduled')->count(),
            'completed'          => $receptionist->appointments()->where('status', 'completed')->count(),
            'cancelled'          => $receptionist->appointments()->where('status', 'cancelled')->count(),
            'patients_registered' => $receptionist->patients()->count(),
        ];

        return view('admin.receptionists.show', compact('receptionist', 'appointments', 'stats'));
    }
}