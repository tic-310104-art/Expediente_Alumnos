<?php

namespace App\Http\Controllers;

use App\Models\HistorialAcademico;
use Illuminate\Http\Request;

class HistorialAcademicoController extends Controller
{
    /**
     * Muestra el Kárdex (Historial Académico) de un alumno.
     */
    public function show($alumnoId)
    {
        $alumno = \App\Models\Alumno::with(['historialAcademico.materia', 'carreras.materias'])->findOrFail($alumnoId);
        $tutor = \App\Models\Tutor::findOrFail($alumno->Tutores_id);
        
        // Obtenemos todas las materias de la carrera del alumno para el dropdown
        $materiasPlan = $alumno->carreras->first() ? $alumno->carreras->first()->materias : collect();

        return view('tutores.gestion_historial', compact('alumno', 'tutor', 'materiasPlan'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['Alumno_id', 'idMateria', 'Materia', 'Profesor', 'Calificacion', 'Horario', 'Ciclo']);

        if ($request->filled('idSubject')) {
            $data['idMateria'] = $request->input('idSubject');
        }
        if ($request->filled('Subject')) {
            $data['Materia'] = $request->input('Subject');
        }
        if ($request->filled('Teacher')) {
            $data['Profesor'] = $request->input('Teacher');
        }
        if ($request->filled('Schedule')) {
            $data['Horario'] = $request->input('Schedule');
        }
        if ($request->filled('Periodo')) {
            $data['Ciclo'] = $request->input('Periodo');
        }
        if ($request->filled('Alumnos_id')) {
            $data['Alumno_id'] = $request->input('Alumnos_id');
        }

        HistorialAcademico::create($data);

        return redirect()->back()->with('success', 'Calificación registrada exitosamente en el historial.');
    }

    public function update(Request $request, $id)
    {
        $historial = HistorialAcademico::findOrFail($id);

        $data = $request->only(['Alumno_id', 'idMateria', 'Materia', 'Profesor', 'Calificacion', 'Horario', 'Ciclo']);

        if ($request->filled('idSubject')) {
            $data['idMateria'] = $request->input('idSubject');
        }
        if ($request->filled('Subject')) {
            $data['Materia'] = $request->input('Subject');
        }
        if ($request->filled('Teacher')) {
            $data['Profesor'] = $request->input('Teacher');
        }
        if ($request->filled('Schedule')) {
            $data['Horario'] = $request->input('Schedule');
        }
        if ($request->filled('Periodo')) {
            $data['Ciclo'] = $request->input('Periodo');
        }
        if ($request->filled('Alumnos_id')) {
            $data['Alumno_id'] = $request->input('Alumnos_id');
        }

        $historial->update($data);

        return redirect()->back()->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $historial = HistorialAcademico::findOrFail($id);
        $historial->delete();
        return redirect()->back()->with('success', 'Materia eliminada del historial.');
    }
}