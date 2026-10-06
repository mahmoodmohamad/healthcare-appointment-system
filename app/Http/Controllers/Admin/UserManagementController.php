<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Admin, City, Doctor, Patient, Receptionist, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        switch ($request->role) {
            case 'admin':        $query->admins(); break;
            case 'doctor':       $query->doctors(); break;
            case 'receptionist': $query->receptionists(); break;
            case 'patient':      $query->patients(); break;
        }

        if ($search = $request->search) {
            $query->search($search);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $cities = City::all();

        return view('admin.users.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'role'           => ['required', Rule::in(['admin', 'doctor', 'receptionist', 'patient'])],
            'phone'          => 'required_if:role,doctor,receptionist,patient',
            'city_id'        => 'required_if:role,doctor,receptionist,patient|nullable|exists:cities,id',
            'specialization' => 'required_if:role,doctor',
            'national_id'    => 'required_if:role,patient|nullable|string|unique:patients,national_id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'activation' => true,
            ]);

            switch ($request->role) {
                case 'admin':
                    Admin::create(['user_id' => $user->id]);
                    break;

                case 'doctor':
                    Doctor::create([
                        'user_id'        => $user->id,
                        'specialization' => $request->specialization,
                        'phone'          => $request->phone,
                        'city_id'        => $request->city_id,
                    ]);
                    break;

                case 'receptionist':
                    Receptionist::create([
                        'user_id' => $user->id,
                        'phone'   => $request->phone,
                        'city_id' => $request->city_id,
                    ]);
                    break;

                case 'patient':
                    Patient::create([
                        'user_id'     => $user->id,
                        'national_id' => $request->national_id,
                        'phone'       => $request->phone,
                        'city_id'     => $request->city_id,
                    ]);
                    break;
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', ucfirst($request->role) . ' created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors(['error' => 'Failed to create user. Please try again.'])->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load(['admin', 'doctor', 'receptionist', 'patient']);

        return view('admin.users.show', compact('user'));
    }

    public function toggleActivation(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate yourself!']);
        }

        $user->update(['activation' => ! $user->activation]);

        // Kill API access immediately when deactivating
        if (! $user->activation) {
            $user->tokens()->delete();
        }

        return back()->with('success', 'User ' . ($user->activation ? 'activated' : 'deactivated') . ' successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself!']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}