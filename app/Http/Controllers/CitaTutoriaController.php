<?php

namespace App\Http\Controllers;

use App\Models\CitaTutoria;
use Illuminate\Http\Request;

class CitaTutoriaController extends Controller
{
    public function index()
    {
        return CitaTutoria::with('tutor')->get();
    }

    public function store(Request $request)
    {
        CitaTutoria::create($request->all());
        return redirect()->back()->with('success', 'Cita agendada correctamente');
    }

    public function edit($id)
    {
        $cita = CitaTutoria::findOrFail($id);
        $tutor = \App\Models\Tutor::with('alumnos')->findOrFail($cita->Tutores_id);
        return view('tutores.edit_cita', compact('cita', 'tutor'));
    }

    public function update(Request $request, $id)
    {
        $cita = CitaTutoria::findOrFail($id);
        $cita->update($request->all());
        return redirect()->route('tutor.citas', $cita->Tutores_id)
            ->with('success', 'Cita reprogramada correctamente.');
    }

    public function destroy($id)
    {
        $cita = CitaTutoria::findOrFail($id);
        $tutorId = $cita->Tutores_id;
        $cita->delete();

        return redirect()->route('tutor.citas', $tutorId)
            ->with('success', 'Cita eliminada correctamente.');
    }
}