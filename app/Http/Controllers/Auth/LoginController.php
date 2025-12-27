<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email','password');
        $remember = $request->has('remember');

        if(Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Redirect based on role
            $user = Auth::user();
            if($user->isAdmin()) return redirect()->route('admin.dashboard');
            if($user->isPhysician()) return redirect()->route('physician.dashboard');
            if($user->isSecretary()) return redirect()->route('secretary.dashboard');
            if($user->isPatient()) return redirect()->route('patient.dashboard');

            return redirect()->intended('/');
        }

        return back()->with('error', 'Invalid credentials')->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
