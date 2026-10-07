<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 60;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // No min length on login: it leaks the password policy and locks out older accounts
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email       = Str::lower(trim($request->input('email')));
        $password    = $request->input('password');
        $remember    = $request->boolean('remember');
        $throttleKey = $email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->with('error', "Too many login attempts. Try again in {$seconds} seconds.")
                ->withInput(['email' => $email]);
        }

        // Tell a deactivated user why (only if the password is correct).
        // Counts as an attempt so it can't be used as a free password oracle.
        $user = User::where('email', $email)->first();
        if ($user && ! $user->activation && Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            return back()
                ->with('error', 'Your account is deactivated. Please contact the administrator.')
                ->withInput(['email' => $email]);
        }

        if (Auth::attempt(['email' => $email, 'password' => $password, 'activation' => true], $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin())        return redirect()->route('admin.dashboard');
            if ($user->isDoctor())       return redirect()->route('doctor.dashboard');
            if ($user->isReceptionist()) return redirect()->route('receptionist.dashboard');
            if ($user->isPatient())      return redirect()->route('patient.dashboard');

            $this->endSession($request);

            return redirect()->route('login')->with('error', 'Your account has no assigned role.');
        }

        RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

        return back()
            ->with('error', 'Invalid credentials')
            ->withInput(['email' => $email]);
    }

    public function logout(Request $request)
    {
        $this->endSession($request);

        return redirect()->route('login');
    }

    private function endSession(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}