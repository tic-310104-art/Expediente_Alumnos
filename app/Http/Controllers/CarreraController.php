<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Grupo; // Se importan los grupos ya que comparten vista
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::with('alumnos')->get();
        $grupos = Grupo::all();
        $tutores = \App\Models\Tutor::all();
        
        return view('carreras.carreras', compact('carreras', 'grupos', 'tutores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
        ]);

        $servicioEscolar = auth()->user()->servicioEscolar;
        
        if ($servicioEscolar) {
            $idServicio = $servicioEscolar->idServicios_Escolares;
        } else {
            $primerServicio = \App\Models\ServicioEscolar::first();
            $idServicio = $primerServicio ? $primerServicio->idServicios_Escolares : null;
        }

        Carrera::create([
            'Nombre' => $request->Nombre,
            'Servicios_Escolares_id' => $idServicio
        ]);

        return redirect()->route('carreras.index')->with('success', __('Carrera creada exitosamente.'));
    }

    public function show($id)
    {
        return Carrera::findOrFail($id);
    }

    public function edit($id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('carreras.edit_carrera', compact('carrera'));
    }

    public function update(Request $request, $id)
    {
        $carrera = Carrera::findOrFail($id);
        $request->validate(['Nombre' => 'required|string|max:255']);
        $carrera->update($request->only(['Nombre']));
        return redirect()->route('carreras.index')->with('success', __('Carrera actualizada correctamente.'));
    }

    public function destroy($id)
    {
        $carrera = Carrera::findOrFail($id);

        // Si se elimina una carrera, sus grupos se eliminan por cascade.
        // Primero debemos desvincular a los alumnos de esos grupos para no violar FK.
        $grupoIds = Grupo::where('idCarreras', $id)->pluck('idGrupos');
        if ($grupoIds->count() > 0) {
            \DB::table('alumnos')
                ->whereIn('Grupos_id', $grupoIds->all())
                ->update(['Grupos_id' => null]);
        }
        
        // Desvincular alumnos de la tabla pivote antes de borrar
        \DB::table('carreras_alumnos')->where('Carreras_id', $id)->delete();
        
        $carrera->delete();
        return redirect()->route('carreras.index')->with('success', __('Carrera eliminada y desvinculada exitosamente.'));
    }
}
