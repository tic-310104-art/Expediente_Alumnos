<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\GrupoMateria;
use App\Models\Materia;
use App\Models\HistorialAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    /**
     * Muestra la interfaz para asignar calificaciones a un alumno basándose en la carga académica del grupo
     */
    public function asignarCalificaciones($id, $alumnoId)
    {
        $alumno = Alumno::with(['grupo', 'carreras'])->findOrFail($alumnoId);
        
        // El middleware EnsureOwnership ya validó que $id sea el del tutor logueado
        // Pero validamos adicionalmente que este alumno sea tutorado de este tutor
        if (Auth::user()->role === 'tutor') {
            if ($alumno->Tutores_id != $id) {
                abort(403, 'No tienes permiso para ver este alumno');
            }
        }

        // Obtener la carga académica del grupo del alumno
        $cargaAcademica = GrupoMateria::with(['materia', 'grupo'])
            ->where('idGrupos', $alumno->Grupos_id)
            ->get();

        return view('tutores.asignar_calificaciones', compact('alumno', 'cargaAcademica'));
    }

    /**
     * Guarda las calificaciones del alumno
     */
    public function guardarCalificaciones(Request $request, $id, $alumnoId)
    {
        $alumno = Alumno::findOrFail($alumnoId);
        
        if (Auth::user()->role === 'tutor') {
            if ($alumno->Tutores_id != $id) {
                abort(403, 'No tienes permiso para modificar este alumno');
            }
        }

        $calificaciones = $request->input('calificaciones', []);
        $periodo = $request->input('Periodo', date('Y').'-'.(date('Y') + 1));

        foreach ($calificaciones as $grupoMateriaId => $calificacion) {
            if ($calificacion === null || $calificacion === '') continue;

            $grupoMateria = GrupoMateria::with('materia')->find($grupoMateriaId);
            if (!$grupoMateria) {
                continue;
            }

            // Guardar o actualizar la calificación en el historial académico
            HistorialAcademico::updateOrCreate(
                [
                    'Alumno_id' => $alumnoId,
                    'idMateria' => $grupoMateria->idMateria
                ],
                [
                    'Calificacion' => $calificacion,
                    'Ciclo' => $periodo,
                    'Profesor' => $grupoMateria->Maestro,
                    'Horario' => $grupoMateria->Horario,
                    'Materia' => $grupoMateria->materia->Nombre ?? null
                ]
            );
        }

        return redirect()->back()->with('success', 'Calificaciones guardadas correctamente en el historial.');
    }

    /**
     * Muestra el historial académico completo (Kárdex) del alumno
     */
    public function verHistorial($id)
    {
        // En este caso $id es el id del alumno, ya que la ruta es alumno/{id}/historial
        $alumno = Alumno::with(['grupo', 'carreras', 'tutor', 'historialAcademico'])->findOrFail($id);
        
        // El middleware EnsureOwnership ya validó que $id sea el del alumno logueado
        // (o admin tiene acceso libre)

        $historial = $alumno->historialAcademico;

        return view('alumnos.historial_completo', compact('alumno', 'historial'));
    }
}
