<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
         $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Check role
        $check = match($role) {
            'admin' => $user->isAdmin(),
            'doctor' => $user->isDoctor(),
            'receptionist' => $user->isReceptionist(),
            'patient' => $user->isPatient(),
            default => false
        };

        if (!$check) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
    
}
