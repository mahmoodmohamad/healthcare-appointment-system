<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Tell a deactivated user why (only if the password is correct)
        $user = User::where('email', $credentials['email'])->first();
        if ($user && ! $user->activation && Hash::check($credentials['password'], $user->password)) {
            return back()
                ->with('error', 'Your account is deactivated. Please contact the administrator.')
                ->withInput($request->only('email'));
        }

        if (Auth::attempt($credentials + ['activation' => true], $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin())        return redirect()->route('admin.dashboard');
            if ($user->isDoctor())       return redirect()->route('doctor.dashboard');
            if ($user->isReceptionist()) return redirect()->route('receptionist.dashboard');
            if ($user->isPatient())      return redirect()->route('patient.dashboard');

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Your account has no assigned role.');
        }

        return back()
            ->with('error', 'Invalid credentials')
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}