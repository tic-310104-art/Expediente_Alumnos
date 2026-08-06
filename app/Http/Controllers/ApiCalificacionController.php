<?php

namespace App\Http\Controllers;

use App\Helpers\PeriodoHelper;
use App\Models\Alumno;
use App\Models\GrupoMateria;
use App\Models\HistorialAcademico;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Endpoints JSON consumidos por la aplicación móvil.
 * Se autentican con la sesión web (cookie) que establece la app móvil
 * al iniciar sesión en /sesion, por lo que no requieren CSRF (ver bootstrap/app.php).
 */
class ApiCalificacionController extends Controller
{
    /**
     * GET /api/materias
     * Devuelve el catálogo de materias para precargar los nombres en la app.
     */
    public function materias(Request $request)
    {
        $query = Materia::query();
        if ($request->filled('carrera')) {
            $query->where('idCarreras', $request->integer('carrera'));
        }

        $materias = $query->orderBy('Nombre')->get()->map(fn (Materia $m) => [
            'idMateria' => $m->idMateria,
            'nombre' => $m->Nombre,
            'cuatrimestre' => $m->Cuatrimestre,
            'idCarreras' => $m->idCarreras,
        ]);

        return response()->json($materias);
    }

    /**
     * GET /api/carga?matricula=X
     * Devuelve la carga académica del grupo del alumno junto con la
     * calificación ya registrada (si existe), igual que la vista web
     * "Asignar Calificaciones".
     */
    public function carga(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string',
        ]);

        $alumno = Alumno::where('Matricula', $request->input('matricula'))->first();
        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $materias = GrupoMateria::with('materia')
            ->where('idGrupos', $alumno->Grupos_id)
            ->get()
            ->map(function (GrupoMateria $gm) use ($alumno) {
                return [
                    'idGrupoMateria' => $gm->id,
                    'idMateria' => $gm->idMateria,
                    'materia' => $gm->materia->Nombre ?? null,
                    'maestro' => $gm->Maestro,
                    'horario' => $gm->Horario,
                    'calificacion' => $gm->historialCalificacion($alumno->idAlumnos),
                ];
            });

        return response()->json([
            'alumno' => [
                'idAlumnos' => $alumno->idAlumnos,
                'nombre' => trim(($alumno->Nombre ?? '') . ' ' . ($alumno->Apellido ?? '')),
                'matricula' => $alumno->Matricula,
                'grupo' => $alumno->grupo->Grupo ?? null,
            ],
            'periodo' => PeriodoHelper::getCurrentPeriodName(),
            'materias' => $materias,
        ]);
    }

    /**
     * POST /api/calificaciones
     * body: { matricula, periodo?, calificaciones: { idGrupoMateria: valor } }
     * Guarda las calificaciones en el historial académico (updateOrCreate por materia).
     */
    public function guardar(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'tutor'], true)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'matricula' => 'required|string',
            'calificaciones' => 'required|array',
        ]);

        $alumno = Alumno::where('Matricula', $request->input('matricula'))->first();
        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $periodo = $request->input('periodo') ?: PeriodoHelper::getCurrentPeriodName();
        $guardadas = [];

        foreach ($request->input('calificaciones', []) as $idGrupoMateria => $calificacion) {
            if ($calificacion === null || $calificacion === '') continue;
            if (!is_numeric($calificacion)) continue;

            $grupoMateria = GrupoMateria::with('materia')->find($idGrupoMateria);
            if (!$grupoMateria) continue;

            HistorialAcademico::updateOrCreate(
                [
                    'Alumno_id' => $alumno->idAlumnos,
                    'idMateria' => $grupoMateria->idMateria,
                ],
                [
                    'Calificacion' => (float) $calificacion,
                    'Ciclo' => $periodo,
                    'Profesor' => $grupoMateria->Maestro,
                    'Horario' => $grupoMateria->Horario,
                    'Materia' => $grupoMateria->materia->Nombre ?? null,
                ]
            );

            $guardadas[] = [
                'idMateria' => $grupoMateria->idMateria,
                'materia' => $grupoMateria->materia->Nombre ?? null,
                'calificacion' => (float) $calificacion,
            ];
        }

        return response()->json([
            'message' => 'Calificaciones guardadas correctamente',
            'guardadas' => $guardadas,
            'promedio' => $alumno->promedio,
        ]);
    }
}
