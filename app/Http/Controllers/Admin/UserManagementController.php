<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Admin, Patient, Physician, Secretary, City};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * List all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($role = $request->role) {
            switch ($role) {
                case 'admin':
                    $query->admins();
                    break;
                case 'physician':
                    $query->physicians();
                    break;
                case 'secretary':
                    $query->secretaries();
                    break;
                case 'patient':
                    $query->patients();
                    break;
            }
        }

        // Search
        if ($search = $request->search) {
            $query->search($search);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $cities = City::all();
        return view('admin.users.create', compact('cities'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'physician', 'secretary', 'patient'])],
            'phone' => 'required_if:role,physician,secretary,patient',
            'city_id' => 'required_if:role,physician,secretary,patient|exists:cities,id',
            'specialization' => 'required_if:role,physician',
            'national_id' => 'required_if:role,secretary,patient|unique:patients,national_id|unique:secretaries,national_id',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activation' => true,
            ]);

            // Create role-specific record
            switch ($request->role) {
                case 'admin':
                    Admin::create(['user_id' => $user->id]);
                    break;

                case 'physician':
                    Physician::create([
                        'user_id' => $user->id,
                        'specialization' => $request->specialization,
                        'phone' => $request->phone,
                        'city_id' => $request->city_id,
                    ]);
                    break;

                case 'secretary':
                    Secretary::create([
                        'user_id' => $user->id,
                        'phone' => $request->phone,
                        'city_id' => $request->city_id,
                    ]);
                    break;

                case 'patient':
                    Patient::create([
                        'user_id' => $user->id,
                        'national_id' => $request->national_id,
                        'phone' => $request->phone,
                        'city_id' => $request->city_id,
                    ]);
                    break;
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', ucfirst($request->role) . ' created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user->load(['admin', 'physician', 'secretary', 'patient']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Toggle user activation
     */
    public function toggleActivation(User $user)
    {
        $user->update(['activation' => !$user->activation]);
        
        $status = $user->activation ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "User {$status} successfully!");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself!']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}