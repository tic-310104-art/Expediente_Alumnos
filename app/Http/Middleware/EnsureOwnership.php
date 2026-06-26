<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnership
{
    /**
     * Verifica que el {id} de la ruta corresponda al perfil del usuario autenticado.
     * Los administradores siempre tienen acceso libre.
     * Si el usuario intenta acceder a un recurso ajeno, se le regresa a la página anterior
     */
    public function handle(Request $request, Closure $next, string $tabla = 'alumnos', string $pk = 'idAlumnos'): Response
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Los administradores tienen acceso absoluto para tareas de supervisión
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Si es un tutor, permitir acceso a registros de alumnos que tenga asignados
        if ($user && $user->role === 'tutor' && $tabla === 'alumnos') {
            $routeId = $request->route('id');
            if ($routeId) {
                // Obtener ID del tutor directamente de la BD usando su user_id
                $tutorId = \Illuminate\Support\Facades\DB::table('tutores')
                    ->where('user_id', $user->id)
                    ->value('idTutores');

                if ($tutorId) {
                    // Verificar si el alumno pertenece a este tutor (directamente o por grupo)
                    $pertenece = \Illuminate\Support\Facades\DB::table('alumnos')
                        ->where($pk, $routeId)
                        ->where(function ($query) use ($tutorId) {
                            $query->where('Tutores_id', $tutorId)
                                  ->orWhereExists(function ($sub) use ($tutorId) {
                                      $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                                          ->from('grupos')
                                          ->whereColumn('grupos.idGrupos', 'alumnos.Grupos_id')
                                          ->where('idTutores', $tutorId);
                                  });
                        })
                        ->exists();
                    
                    if ($pertenece) {
                        return $next($request);
                    }
                }
            }
        }

        // Obtener el {id} que viene en la URL
        $routeId = $request->route('id');

        // Si no hay un {id} en la ruta, dejar pasar (rutas sin parámetro de id)
        if ($routeId === null) {
            return $next($request);
        }

        // Buscar el registro relacionado al usuario autenticado en la tabla correspondiente
        $perfil = \Illuminate\Support\Facades\DB::table($tabla)
            ->where('user_id', $user->id)
            ->first();

        // Si no se encontró perfil, regresar silenciosamente a su dashboard
        if (!$perfil) {
            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();
            if ($previousUrl && $previousUrl !== $currentUrl) return redirect()->back();

            if ($user->role === 'tutor') {
                return redirect()->route('tutor.dashboard', ['id' => $user->tutor?->idTutores ?? 0]);
            } elseif ($user->role === 'alumno') {
                return redirect()->route('alumno.dashboard', ['id' => $user->alumno?->idAlumnos ?? 0]);
            }
            return redirect()->route('login');
        }

        // Comparar el ID del perfil con el {id} solicitado en la ruta
        $perfilId = $perfil->$pk;

        if ((int) $routeId !== (int) $perfilId) {
            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();
            if ($previousUrl && $previousUrl !== $currentUrl) return redirect()->back();

            if ($user->role === 'tutor') {
                return redirect()->route('tutor.dashboard', ['id' => $user->tutor?->idTutores ?? 0]);
            } elseif ($user->role === 'alumno') {
                return redirect()->route('alumno.dashboard', ['id' => $user->alumno?->idAlumnos ?? 0]);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
