<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Admin, Appointment, City, Doctor, Patient, Receptionist, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    private const ROLES = ['admin', 'doctor', 'receptionist', 'patient'];

    public function index(Request $request)
    {
        $query = User::query()->with(self::ROLES);

        match ($request->query('role')) {
            'admin'        => $query->admins(),
            'doctor'       => $query->doctors(),
            'receptionist' => $query->receptionists(),
            'patient'      => $query->patients(),
            default        => null,
        };

        if ($search = trim((string) $request->query('search', ''))) {
            $query->search($search);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $cities = City::all();

        return view('admin.users.create', compact('cities'));
    }

    public function store(Request $request)
    {
        // Normalise before the unique check so casing can't create duplicates
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'confirmed', Password::min(10)],
            'role'           => ['required', Rule::in(self::ROLES)],
            'phone'          => ['required_if:role,doctor,receptionist,patient', 'nullable', 'string', 'max:30'],
            'city_id'        => ['required_if:role,doctor,receptionist,patient', 'nullable', 'exists:cities,id'],
            'specialization' => ['required_if:role,doctor', 'nullable', 'string', 'max:255'],
            'national_id'    => ['required_if:role,patient', 'nullable', 'string', 'max:50', 'unique:patients,national_id'],
        ]);

        try {
            $user = DB::transaction(function () use ($data) {
                $user = new User([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                // activation is not mass-assignable on purpose
                $user->forceFill(['activation' => true])->save();

                match ($data['role']) {
                    'admin' => Admin::create(['user_id' => $user->id]),

                    'doctor' => Doctor::create([
                        'user_id'        => $user->id,
                        'specialization' => $data['specialization'],
                        'phone'          => $data['phone'],
                        'city_id'        => $data['city_id'],
                    ]),

                    'receptionist' => Receptionist::create([
                        'user_id' => $user->id,
                        'phone'   => $data['phone'],
                        'city_id' => $data['city_id'],
                    ]),

                    'patient' => Patient::create([
                        'user_id'     => $user->id,
                        'national_id' => $data['national_id'],
                        'phone'       => $data['phone'],
                        'city_id'     => $data['city_id'],
                    ]),
                };

                return $user;
            });

            Log::info('admin.user.created', [
                'actor_id' => auth()->id(),
                'user_id'  => $user->id,
                'role'     => $data['role'],
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', ucfirst($data['role']) . ' created successfully!');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors(['error' => 'Failed to create user. Please try again.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function show(User $user)
    {
        $user->load(self::ROLES);

        return view('admin.users.show', compact('user'));
    }

    public function toggleActivation(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate yourself!']);
        }

        DB::transaction(function () use ($user) {
            $user->forceFill(['activation' => ! $user->activation])->save();

            // Kill API access immediately when deactivating
            if (! $user->activation) {
                $user->tokens()->delete();
            }
        });

        Log::info('admin.user.activation_toggled', [
            'actor_id'   => auth()->id(),
            'user_id'    => $user->id,
            'activation' => $user->activation,
        ]);

        return back()->with('success', 'User ' . ($user->activation ? 'activated' : 'deactivated') . ' successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself!']);
        }

        // Never hard-delete users that are part of clinical records
        if ($this->hasClinicalRecords($user)) {
            return back()->withErrors([
                'error' => 'This user has appointments on record and cannot be deleted. Deactivate the account instead.',
            ]);
        }

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->delete();
        });

        Log::info('admin.user.deleted', [
            'actor_id' => auth()->id(),
            'user_id'  => $user->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    private function hasClinicalRecords(User $user): bool
    {
        $columns = array_filter([
            'doctor_id'       => $user->doctor?->id,
            'patient_id'      => $user->patient?->id,
            'receptionist_id' => $user->receptionist?->id,
        ]);

        if ($columns === []) {
            return false;
        }

        return Appointment::query()
            ->where(function ($q) use ($columns) {
                foreach ($columns as $column => $id) {
                    $q->orWhere($column, $id);
                }
            })
            ->exists();
    }
}