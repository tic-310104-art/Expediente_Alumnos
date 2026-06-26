<?php

namespace App\Http\Controllers;

use App\Models\CitaPsicologia;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaPsicologiaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            abort(403);
        }

        $tutor = $user->tutor()->with('alumnos')->firstOrFail();
        $citas = CitaPsicologia::where('Tutores_id', $tutor->idTutores)
            ->with(['alumnos'])
            ->orderBy('Fecha', 'desc')
            ->get();

        return view('tutores.tutor_psicologia', compact('tutor', 'citas'));
    }

    public function create()
    {
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            abort(403);
        }
        $tutor = $user->tutor;

        $request->validate([
            'Tutores_id' => 'required|exists:tutores,idTutores',
            'Alumno_id' => 'required|exists:alumnos,idAlumnos',
            'Fecha' => 'required',
            'Asistencia' => 'required|string|max:255',
        ]);

        if (!$tutor || (string) $tutor->idTutores !== (string) $request->Tutores_id) {
            abort(403);
        }

        $cita = CitaPsicologia::create($request->only(['Fecha', 'Asistencia', 'Tutores_id']));
        $cita->alumnos()->sync([(int) $request->Alumno_id]);
        return redirect()->back()->with('success', 'Cita de Psicología agendada correctamente.');
    }

    public function show($id)
    {
        return CitaPsicologia::with(['tutor', 'alumnos'])->findOrFail($id);
    }

    public function edit($id)
    {
        $cita = CitaPsicologia::findOrFail($id);
        return redirect()->route('tutor.psicologia', ['id' => $cita->Tutores_id, 'edit_cita_psicologia' => $cita->idCita]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            abort(403);
        }
        $tutor = $user->tutor;

        $request->validate([
            'Alumno_id' => 'required|exists:alumnos,idAlumnos',
            'Fecha' => 'required',
            'Asistencia' => 'required|string|max:255',
        ]);

        $cita = CitaPsicologia::findOrFail($id);
        if (!$tutor || (string) $cita->Tutores_id !== (string) $tutor->idTutores) {
            abort(403);
        }

        $cita->update($request->only(['Fecha', 'Asistencia']));
        $cita->alumnos()->sync([(int) $request->Alumno_id]);
        return redirect()->back()->with('success', 'Cita de Psicología actualizada.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            abort(403);
        }
        $tutor = $user->tutor;

        $cita = CitaPsicologia::findOrFail($id);
        if (!$tutor || (string) $cita->Tutores_id !== (string) $tutor->idTutores) {
            abort(403);
        }

        $cita->alumnos()->detach();
        $cita->delete();
        return redirect()->back()->with('success', 'Cita de Psicología eliminada.');
    }
}
