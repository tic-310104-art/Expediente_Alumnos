<?php

namespace App\Http\Controllers;

use App\Models\CitaTutoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaTutoriaController extends Controller
{
    public function index()
    {
        return CitaTutoria::with('tutor')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Motivo' => 'required|string|max:500',
            'Alumnos_id' => 'required|exists:alumnos,idAlumnos',
            'Tutores_id' => 'required|exists:tutores,idTutores',
        ]);

        if (Auth::user()->role === 'tutor' && $request->Tutores_id != Auth::user()->tutor->idTutores) {
            return redirect()->back()->with('error', __('No tienes permiso para agendar citas para otro tutor.'));
        }

        CitaTutoria::create($request->only(['Fecha', 'Motivo', 'Alumnos_id', 'Tutores_id']));
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

        if (Auth::user()->role === 'tutor' && $cita->Tutores_id != Auth::user()->tutor->idTutores) {
            return redirect()->back()->with('error', __('No tienes permiso para modificar esta cita.'));
        }

        $request->validate([
            'Fecha' => 'required|date',
            'Motivo' => 'required|string|max:500',
        ]);

        $cita->update($request->only(['Fecha', 'Motivo']));
        return redirect()->route('tutor.citas', $cita->Tutores_id)
            ->with('success', 'Cita reprogramada correctamente.');
    }

    public function destroy($id)
    {
        $cita = CitaTutoria::findOrFail($id);

        if (Auth::user()->role === 'tutor' && $cita->Tutores_id != Auth::user()->tutor->idTutores) {
            return redirect()->back()->with('error', __('No tienes permiso para eliminar esta cita.'));
        }

        $tutorId = $cita->Tutores_id;
        $cita->delete();

        return redirect()->route('tutor.citas', $tutorId)
            ->with('success', 'Cita eliminada correctamente.');
    }
}