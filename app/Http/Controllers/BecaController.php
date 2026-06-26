<?php

namespace App\Http\Controllers;

use App\Models\Beca;
use App\Models\Alumno;
use Illuminate\Http\Request;

class BecaController extends Controller
{
    public function index()
    {
        $becas = Beca::withCount('alumnos')->get();
        $alumnos = Alumno::all();
        return view('becas.index', compact('becas', 'alumnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'Monto' => 'nullable|numeric'
        ]);

        Beca::create($request->all());

        return redirect()->route('becas.index')->with('success', __('Beca creada correctamente.'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'Alumno_id' => 'required|exists:alumnos,idAlumnos',
            'Beca_id' => 'required|exists:becas,idBecas',
            'Fecha_Asignacion' => 'required|date'
        ]);

        $alumno = Alumno::findOrFail($request->Alumno_id);
        $alumno->becas()->attach($request->Beca_id, ['Fecha_Asignacion' => $request->Fecha_Asignacion]);

        return redirect()->route('becas.index')->with('success', __('Beca asignada correctamente.'));
    }

    public function show($id)
    {
        $beca = Beca::with('alumnos')->findOrFail($id);
        return view('becas.show', compact('beca'));
    }

    public function edit($id)
    {
        $beca = Beca::findOrFail($id);
        return view('becas.edit', compact('beca'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'Monto' => 'nullable|numeric'
        ]);

        $beca = Beca::findOrFail($id);
        $beca->update($request->all());

        return redirect()->route('becas.index')->with('success', __('Beca actualizada correctamente.'));
    }

    public function destroy($id)
    {
        $beca = Beca::findOrFail($id);
        $beca->delete();
        return redirect()->route('becas.index')->with('success', __('Beca eliminada correctamente.'));
    }

    public function unassign(Request $request, $beca_id, $alumno_id)
    {
        $beca = Beca::findOrFail($beca_id);
        $beca->alumnos()->detach($alumno_id);

        return redirect()->route('becas.show', $beca_id)->with('success', __('Asignación eliminada correctamente.'));
    }
}
