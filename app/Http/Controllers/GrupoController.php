<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Carrera;
use App\Models\Tutor;
use App\Models\Materia;
use App\Models\GrupoMateria;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        return redirect()->route('carreras.index');
    }

    public function store(Request $request)
    {
        $grupo = Grupo::create($request->all());
        
        if ($request->filled('idCarreras')) {
            return redirect()->route('materias.show', $request->idCarreras)
                ->with('success', __('Grupo creado exitosamente en esta carrera.'));
        }

        return redirect()->route('carreras.index')->with('success', __('Grupo creado exitosamente.'));
    }

    public function manageCarga($id)
    {
        $grupo = Grupo::with(['carrera.materias', 'materias', 'tutor'])->findOrFail($id);
        $materiasCarrera = $grupo->carrera ? $grupo->carrera->materias : collect();
        
        return view('carreras.manage_carga', compact('grupo', 'materiasCarrera'));
    }

    public function storeCarga(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
        
        $request->validate([
            'materias' => 'nullable|array',
            'materias.*.idMateria' => 'required|exists:materias,idMateria',
            'materias.*.Maestro' => 'nullable|string|max:255',
            'materias.*.Horario' => 'nullable|string|max:255',
        ]);

        $syncData = [];
        if ($request->has('materias')) {
            foreach ($request->materias as $materiaData) {
                if (isset($materiaData['selected']) && $materiaData['selected'] == '1') {
                    $idMateria = $materiaData['idMateria'] ?? null;
                    if ($idMateria) {
                        $syncData[$idMateria] = [
                            'Maestro' => isset($materiaData['Maestro']) ? $materiaData['Maestro'] : null,
                            'Horario' => isset($materiaData['Horario']) ? $materiaData['Horario'] : null,
                        ];
                    }
                }
            }
        }

        try {
            // Usamos sync para que lo que no esté en el array se elimine automáticamente
            $grupo->materias()->sync($syncData);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error guardando carga académica: ' . $e->getMessage());
            return redirect()->back()->withErrors(['Error interno al guardar materias: ' . $e->getMessage()]);
        }

        return redirect()->route('materias.show', $grupo->idCarreras)
            ->with('success', __('Carga académica actualizada correctamente para el grupo :grupo.', ['grupo' => $grupo->Grupo]));
    }

    public function show($id)
    {
        return Grupo::with(['carrera', 'tutor', 'materias'])->findOrFail($id);
    }

    public function edit($id)
    {
        $grupo = Grupo::findOrFail($id);
        $carreras = Carrera::all();
        // Filtrar tutores que pertenezcan a la carrera del grupo
        $tutores = Tutor::where('idCarreras', $grupo->idCarreras)->get();
        return view('carreras.edit_grupo', compact('grupo', 'carreras', 'tutores'));
    }

    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
        $oldTutor = $grupo->idTutores;

        $grupo->update($request->all());

        // Si el valor de idTutores está presente en la solicitud y cambió, actualizamos a los alumnos
        if ($request->has('idTutores') && $request->idTutores != $oldTutor) {
            $nuevoTutor = empty($request->idTutores) ? null : $request->idTutores;
            \App\Models\Alumno::where('Grupos_id', $grupo->idGrupos)
                ->update(['Tutores_id' => $nuevoTutor]);
        }

        return redirect()->route('carreras.index')->with('success', __('Grupo actualizado correctamente.'));
    }

    public function destroy($id)
    {
        $grupo = Grupo::findOrFail($id);
        
        // Desvincular a los alumnos que pertenecen a este grupo
        \App\Models\Alumno::where('Grupos_id', $id)->update(['Grupos_id' => null]);
        
        $grupo->delete();
        return redirect()->route('carreras.index')->with('success', 'Grupo eliminado. Alumnos desvinculados.');
    }
}