<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  El rol requerido para acceder
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Solo el administrador tiene acceso a todo el sistema.
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Si el rol no coincide con el requerido, redirigir a su propio dashboard
        if ($user->role !== $role) {
            // Intentar regresar a la anterior, pero si no hay una o es la misma, ir al dashboard
            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();

            // Si detectamos que previousUrl es la misma que la actual para evitar loop infinito
            if ($previousUrl && $previousUrl !== $currentUrl && !str_contains($previousUrl, 'sesion')) {
                return redirect()->back();
            }

            // Redirección segura según el rol actual del usuario
            return $this->redirectBasedOnUserRole($user);
        }

        return $next($request);
    }

    /**
     * Redirige al usuario a su dashboard principal según su rol.
     */
    private function redirectBasedOnUserRole($user)
    {
        if ($user->role === 'tutor') {
            $id = $user->tutor?->idTutores;
            return $id ? redirect()->route('tutor.dashboard', ['id' => $id]) : redirect()->route('login');
        } elseif ($user->role === 'alumno') {
            $id = $user->alumno?->idAlumnos;
            return $id ? redirect()->route('alumno.dashboard', ['id' => $id]) : redirect()->route('login');
        } elseif ($user->role === 'admin') {
            return redirect()->route('expedienteGeneral');
        }
        
        return redirect()->route('login');
    }
}
