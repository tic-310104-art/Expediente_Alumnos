<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Carrera;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Muestra el catálogo de materias de una carrera específica.
     */
    public function show($carreraId)
    {
        $carrera = Carrera::with(['materias', 'grupos.tutor'])->findOrFail($carreraId);
        $tutores = \App\Models\Tutor::where('idCarreras', $carreraId)->orWhereNull('idCarreras')->get();
        return view('carreras.gestion_materias', compact('carrera', 'tutores'));
    }

    /**
     * Almacena una nueva materia vinculada a una carrera.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'Cuatrimestre' => 'required|integer|min:1|max:14',
            'idCarreras' => 'required|exists:carreras,idCarreras',
        ]);

        Materia::create($request->all());

        return redirect()->back()->with('success', 'Materia añadida exitosamente al plan de estudios.');
    }

    /**
     * Actualiza una materia.
     */
    public function update(Request $request, $id)
    {
        $materia = Materia::findOrFail($id);
        $materia->update($request->all());

        return redirect()->back()->with('success', 'Materia actualizada correctamente.');
    }

    /**
     * Elimina una materia del catálogo.
     */
    public function destroy($id)
    {
        $materia = Materia::findOrFail($id);
        $materia->delete();

        return redirect()->back()->with('success', 'Materia eliminada del plan de estudios.');
    }
}
