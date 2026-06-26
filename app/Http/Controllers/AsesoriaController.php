<?php

namespace App\Http\Controllers;

use App\Models\Asesoria;
use Illuminate\Http\Request;

class AsesoriaController extends Controller
{
    public function index()
    {
        $asesorias = Asesoria::with('alumnos')->get();
        return view('tutores.tutor_asesorias', compact('asesorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Motivo' => 'required|string|max:255',
            'Fecha' => 'required',
            'Alumno_id' => 'required|array|min:1',
            'Alumno_id.*' => 'integer|exists:alumnos,idAlumnos',
        ]);

        $asesoria = Asesoria::create($request->only(['Motivo', 'Fecha']));
        
        $asesoria->alumnos()->sync($request->Alumno_id);

        return redirect()->back()->with('success', 'Asesoría programada correctamente.');
    }

    public function show($id)
    {
        return Asesoria::with('alumnos')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Motivo' => 'required|string|max:255',
            'Fecha' => 'required',
            'Alumno_id' => 'required|array|min:1',
            'Alumno_id.*' => 'integer|exists:alumnos,idAlumnos',
        ]);

        $asesoria = Asesoria::findOrFail($id);
        $asesoria->update($request->only(['Motivo', 'Fecha']));
        
        $asesoria->alumnos()->sync($request->Alumno_id);

        return redirect()->back()->with('success', 'Asesoría actualizada correctamente.');
    }

    public function destroy($id)
    {
        $asesoria = Asesoria::findOrFail($id);
        $asesoria->alumnos()->detach();
        $asesoria->delete();

        return redirect()->back()->with('success', 'Asesoría eliminada correctamente.');
    }
}
