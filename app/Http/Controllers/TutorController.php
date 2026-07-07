<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\Grupo;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class TutorController extends Controller
{
    private function hydrateAlumnosAsignados(Tutor $tutor): void
    {
        $tutorId = $tutor->idTutores;
        $grupoIds = Grupo::where('idTutores', $tutorId)->pluck('idGrupos');

        $alumnos = Alumno::with('grupo')
            ->where('Tutores_id', $tutorId)
            ->orWhereIn('Grupos_id', $grupoIds)
            ->get()
            ->unique('idAlumnos')
            ->values();

        $tutor->setRelation('alumnos', $alumnos);
    }
    public function index()
    {
         $tutores = Tutor::with(['servicioEscolar', 'carrera', 'alumnos', 'grupos'])->get();
         $carreras = \App\Models\Carrera::all();
         $grupos = \App\Models\Grupo::with(['carrera', 'tutor'])->orderBy('Grupo')->get();

    return view('admins.gestion_tutores', compact('tutores', 'carreras', 'grupos'));
    }

    public function assignAlumno(Request $request)
    {
        $request->validate([
            'idTutores' => 'required|exists:tutores,idTutores',
            'idAlumnos' => 'required|exists:alumnos,idAlumnos'
        ]);

        $alumno = \App\Models\Alumno::findOrFail($request->idAlumnos);
        $alumno->update(['Tutores_id' => $request->idTutores]);

        return back()->with('success', __('Alumno asignado al tutor correctamente.'));
    }

    public function assignGrupo(Request $request)
    {
        $request->validate([
            'idTutores' => 'required|exists:tutores,idTutores',
            'idGrupos' => 'required|exists:grupos,idGrupos'
        ]);

        $grupo = \App\Models\Grupo::findOrFail($request->idGrupos);
        $grupo->update(['idTutores' => $request->idTutores]);

        $alumnos = \App\Models\Alumno::where('Grupos_id', $grupo->idGrupos)->get();
        foreach ($alumnos as $alumno) {
            $alumno->update([
                'Tutores_id' => $request->idTutores
            ]);
            
            if (!$alumno->carreras()->where('carreras.idCarreras', $grupo->idCarreras)->exists()) {
                $alumno->carreras()->attach($grupo->idCarreras);
            }
        }

        return back()->with('success', __('Grupo asignado al tutor correctamente.'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'Apellido' => 'required|string|max:255',
            'Clave_Trabajador' => 'required|string|unique:tutores,Clave_Trabajador',
            'Correo_inst' => 'required|email|unique:tutores,Correo_inst',
            'Password' => 'required|min:6',
            'idCarreras' => 'required|exists:carreras,idCarreras'
        ]);

        $data = $request->all();
        $data['Password'] = Hash::make($request->Password);
        
        Tutor::create($data);

        return redirect()->route('tutores.index')
            ->with('success', __('Tutor creado correctamente'));
    }

    public function show($id)
    {
        return redirect()->route('tutores.index');
    }

    public function edit($id)
    {
        $tutor = Tutor::findOrFail($id);
        $carreras = \App\Models\Carrera::all();
        return view('tutores.edit_tutores', compact('tutor', 'carreras'));
    }

    public function update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);
        
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'Apellido' => 'required|string|max:255',
            'Clave_Trabajador' => 'required|string|unique:tutores,Clave_Trabajador,'.$id.',idTutores',
            'Correo_inst' => 'required|email|unique:tutores,Correo_inst,'.$id.',idTutores',
            'idCarreras' => 'required|exists:carreras,idCarreras'
        ]);

        $data = $request->all();
        
        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        } else {
            unset($data['Password']);
        }
        
        $tutor->update($data);

        return redirect()->route('tutores.index')
            ->with('success', __('Tutor actualizado correctamente'));
    }

    public function destroy($id)
    {
       $tutor = Tutor::findOrFail($id);

        // 1. Desvinculamos a los alumnos (esto ya lo teníamos)
        $tutor->alumnos()->update(['Tutores_id' => null]);

        // 1.1 Desvinculamos grupos asignados a este tutor
        \App\Models\Grupo::where('idTutores', $id)->update(['idTutores' => null]);

        // 2. Desvinculamos sus Citas de Psicología
        \App\Models\CitaPsicologia::where('Tutores_id', $id)->update(['Tutores_id' => null]);

        // 3. Desvinculamos sus Citas de Tutorías
        \App\Models\CitaTutoria::where('Tutores_id', $id)->update(['Tutores_id' => null]);

        // 4. Ahora sí, el tutor está libre. ¡Lo borramos!
        $tutor->delete();

        return redirect()->route('tutores.index')
            ->with('success', 'Tutor eliminado y todas sus relaciones fueron desvinculadas.');
    }

    public function dashboard($id = null)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Si es admin, puede ver el dashboard de cualquier tutor
        if ($user->role === 'admin') {
            $tutor = Tutor::with(['citasTutorias.alumno', 'alumnos', 'grupos.carrera', 'grupos.alumnos.historialAcademico'])->where('idTutores', $id)->firstOrFail();
            $this->hydrateAlumnosAsignados($tutor);
            
            $citasCalendar = $tutor->citasTutorias->flatMap(function($cita) {
                $isPast = \Carbon\Carbon::parse($cita->Fecha)->isPast();
                $baseColor = $isPast ? '#dc2626' : '#2b7a78';
                $bgColor = $isPast ? 'rgba(220, 38, 38, 0.1)' : 'rgba(43, 122, 120, 0.1)';
                
                return [
                    [
                        'title' => ($cita->alumno ? $cita->alumno->Nombre . ' ' . $cita->alumno->Apellido : 'Cita'),
                        'start' => $cita->Fecha,
                        'color' => $baseColor,
                        'description' => $cita->Motivo
                    ],
                    [
                        'start' => $cita->Fecha,
                        'display' => 'background',
                        'color' => $isPast ? 'rgba(220, 38, 38, 0.5)' : 'rgba(34, 197, 94, 0.5)',
                        'allDay' => true
                    ]
                ];
            });

            $atRiskList = $tutor->alumnos->filter(function ($a) {
                return strtolower((string) ($a->Estatus ?? 'activo')) === 'riesgo' || ($a->promedio > 0 && $a->promedio < 8.5);
            })->map(function($a) {
                return [
                    'idAlumnos' => $a->idAlumnos,
                    'Nombre'    => $a->Nombre,
                    'Apellido'  => $a->Apellido,
                    'Matricula' => $a->Matricula,
                    'promedio'  => $a->promedio
                ];
            })->values();
            $riesgoCount = $atRiskList->count();

            return view('tutores.tutor', compact('tutor', 'riesgoCount', 'citasCalendar', 'atRiskList'));
        }

        // Si es tutor, solo puede ver su propio dashboard
        if ($user->role === 'tutor') {
            $ownId = $user->tutor->idTutores ?? null;
            if ($id !== null && (int)$id !== (int)$ownId) {
                return redirect()->route('tutor.dashboard', ['id' => $ownId]);
            }
            
            $tutorId = $ownId;
            $tutor = Tutor::with(['citasTutorias.alumno', 'alumnos', 'grupos.carrera', 'grupos.alumnos.historialAcademico'])->where('idTutores', $tutorId)->firstOrFail();
            $this->hydrateAlumnosAsignados($tutor);

            $citasCalendar = $tutor->citasTutorias->flatMap(function($cita) {
                $isPast = \Carbon\Carbon::parse($cita->Fecha)->isPast();
                $baseColor = $isPast ? '#dc2626' : '#2b7a78';
                $bgColor = $isPast ? 'rgba(220, 38, 38, 0.1)' : 'rgba(43, 122, 120, 0.1)';
                
                return [
                    [
                        'title' => ($cita->alumno ? $cita->alumno->Nombre . ' ' . $cita->alumno->Apellido : 'Cita'),
                        'start' => $cita->Fecha,
                        'color' => $baseColor,
                        'description' => $cita->Motivo
                    ],
                    [
                        'start' => $cita->Fecha,
                        'display' => 'background',
                        'color' => $isPast ? 'rgba(220, 38, 38, 0.5)' : 'rgba(34, 197, 94, 0.5)',
                        'allDay' => true
                    ]
                ];
            });

            $atRiskList = $tutor->alumnos->filter(function ($a) {
                return strtolower((string) ($a->Estatus ?? 'activo')) === 'riesgo' || ($a->promedio > 0 && $a->promedio < 8.5);
            })->map(function($a) {
                return [
                    'idAlumnos' => $a->idAlumnos,
                    'Nombre'    => $a->Nombre,
                    'Apellido'  => $a->Apellido,
                    'Matricula' => $a->Matricula,
                    'promedio'  => $a->promedio
                ];
            })->values();
            $riesgoCount = $atRiskList->count();

            return view('tutores.tutor', compact('tutor', 'riesgoCount', 'citasCalendar', 'atRiskList'));
        }

        return redirect()->route('login');
    }

    public function misCitas(Request $request, $id)
    {
        $tutor = Tutor::with(['alumnos'])->findOrFail($id);
        $alumno_id = $request->query('alumno_id');
        
        $query = \App\Models\CitaTutoria::where('Tutores_id', $id)->with('alumno');
        if ($alumno_id) {
            $query->where('Alumnos_id', $alumno_id);
        }
        $citas = $query->orderBy('Fecha', 'desc')->get();
        
        return view('tutores.tutor_citas', compact('tutor', 'alumno_id', 'citas'));
    }

    public function misPsicologias(Request $request, $id)
    {
        $tutor = Tutor::with(['alumnos'])->findOrFail($id);
        
        $query = \App\Models\CitaPsicologia::where('Tutores_id', $id)
            ->with(['tutor', 'alumnos']);

        if ($request->has('alumno_id')) {
            $alumnoId = (int)$request->alumno_id;
            $query->whereHas('alumnos', function($q) use ($alumnoId) {
                $q->where('Alumno_id', $alumnoId);
            });
        }

        $citas = $query->orderBy('Fecha', 'desc')->get();
        return view('tutores.tutor_psicologia', compact('tutor', 'citas'));
    }

    public function misAsesorias($id)
    {
        $tutor = Tutor::with('alumnos')->findOrFail($id);

        // Filtrar solo asesorías que tengan al menos un alumno asignado a este tutor
        $asesorias = \App\Models\Asesoria::with('alumnos')
            ->whereHas('alumnos', function ($q) use ($id) {
                $q->where('Tutores_id', $id);
            })
            ->orderBy('Fecha', 'desc')
            ->get();

        return view('tutores.tutor_asesorias', compact('tutor', 'asesorias'));
    }
}
