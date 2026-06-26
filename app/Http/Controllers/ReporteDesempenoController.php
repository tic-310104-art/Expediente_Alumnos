<?php

namespace App\Http\Controllers;

use App\Models\ReporteDesempeno;
use App\Models\Alumno;
use App\Models\Tutor;
use Illuminate\Http\Request;

class ReporteDesempenoController extends Controller
{
    /**
     * Muestra la gestión de reportes para un alumno específico.
     */
    public function show($alumnoId)
    {
        $alumno = Alumno::with(['reportesDesempeno', 'historialAcademico'])->findOrFail($alumnoId);
        $tutor = Tutor::findOrFail($alumno->Tutores_id);
        
        return view('tutores.gestion_reportes_alumno', compact('alumno', 'tutor'));
    }

    /**
     * Guarda un nuevo reporte de desempeño.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Alumno_id' => 'required',
            'Tutor_id' => 'required',
            'Fecha' => 'required|date',
            'Nivel_Riesgo' => 'required',
            'Observaciones' => 'required',
        ]);

        ReporteDesempeno::create($request->all());

        return redirect()->back()->with('success', 'Reporte guardado exitosamente en la bitácora.');
    }

    /**
     * Muestra el formulario de edición (si se requiere vista separada, pero lo manejaremos inline o modal).
     */
    public function edit($id)
    {
        $reporte = ReporteDesempeno::findOrFail($id);
        return $reporte; // Para AJAX o carga dinámica
    }

    /**
     * Actualiza un reporte existente.
     */
    public function update(Request $request, $id)
    {
        $reporte = ReporteDesempeno::findOrFail($id);
        $reporte->update($request->all());

        return redirect()->back()->with('success', 'Reporte actualizado correctamente.');
    }

    /**
     * Elimina un reporte.
     */
    public function destroy($id)
    {
        $reporte = ReporteDesempeno::findOrFail($id);
        $reporte->delete();

        return redirect()->back()->with('success', 'Reporte eliminado de la bitácora.');
    }
}
