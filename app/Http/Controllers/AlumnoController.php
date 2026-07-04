<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Grupo;
use App\Models\Tutor;
use App\Models\CitaTutoria;
use App\Models\CitaPsicologia;
use App\Models\Asesoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index()
    {
      $alumnos = Alumno::with(['grupo', 'tutor', 'servicioEscolar', 'carreras'])->get();
        $carreras = Carrera::all();
        $grupos = Grupo::all();
        $tutores = Tutor::all();

        return view('admins.gestion_alumnos', compact('alumnos', 'carreras', 'grupos', 'tutores'));
    }

    public function store(Request $request)
    {
        $data = $request->except('Carreras_id');

        if ($request->filled('Grupos_id')) {
            $grupo = Grupo::where('idGrupos', $request->Grupos_id)->first();
            if ($grupo && !empty($grupo->idTutores)) {
                $data['Tutores_id'] = $grupo->idTutores;
            }
        }

        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }
        $alumno = Alumno::create($data);
        
        if ($request->filled('Carreras_id')) {
            $alumno->carreras()->sync([$request->Carreras_id]);
        }

        return redirect()->route('alumnos.index')
            ->with('success', __('Alumno registrado correctamente.'));
    }

    public function show($id)
    {
        return Alumno::findOrFail($id);
    }

    public function edit($id)
    {
        $alumno = Alumno::with('carreras')->findOrFail($id);
        $carreras = Carrera::all();
        $grupos = Grupo::all();
        $tutores = Tutor::all();
        
        return view('alumnos.edit_alumno', compact('alumno', 'carreras', 'grupos', 'tutores'));
    }

    public function update(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);
        $data = $request->except('Carreras_id');

        if ($request->filled('Grupos_id')) {
            $grupo = Grupo::where('idGrupos', $request->Grupos_id)->first();
            if ($grupo && !empty($grupo->idTutores)) {
                $data['Tutores_id'] = $grupo->idTutores;
            } else {
                $data['Tutores_id'] = null;
            }
        }
        
        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        } else {
            unset($data['Password']);
        }
        
        $alumno->update($data);
        
        if ($request->filled('Carreras_id')) {
            $alumno->carreras()->sync([$request->Carreras_id]);
        }

        return redirect()->route('alumnos.index')
            ->with('success', __('Datos del alumno actualizados correctamente.'));
    }

    public function destroy($id)
    {
        $alumno = Alumno::findOrFail($id);

        // Desvincular de carreras (Pivot table carreras_alumnos)
        $alumno->carreras()->detach();

        // Desvincular de asesorias (Pivot table alumnos_asesoria)
        $alumno->asesorias()->detach();
        
        // Desvincular de citas de psicología (Pivot table citas_psicologia_alumnos)
        $alumno->citasPsicologia()->detach();
        
        // Desvincular de becas (Pivot table alumno_beca)
        $alumno->becas()->detach();

        // Eliminar historial académico (HasMany — Alumno_id es NOT NULL)
        DB::table('historial_academico')
            ->where('Alumno_id', $id)
            ->delete();

        // Eliminar reportes de desempeño (HasMany — Alumno_id es NOT NULL)
        DB::table('reportes_desempeno')
            ->where('Alumno_id', $id)
            ->delete();
            
        // Nullificar citas de tutoría (HasMany)
        DB::table('citas_tutorias')
            ->where('Alumnos_id', $id)
            ->update(['Alumnos_id' => null]);

        $alumno->delete();

        return redirect()->route('alumnos.index')
            ->with('success', __('Alumno eliminado y su historial ha sido desvinculado.'));
    }

    public function updateFoto(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => __('No autorizado')], 403);
            }

            $model = null;

            if ($user->role === 'alumno') {
                $model = $user->alumno;
            } elseif ($user->role === 'tutor') {
                $model = $user->tutor;
            } elseif ($user->role === 'admin') {
                $model = $user->servicioEscolar;
            }

            if ($model) {
                // Verificar que la columna foto_url exista en la tabla
                if (!Schema::hasColumn($model->getTable(), 'foto_url')) {
                    return response()->json(['success' => false, 'message' => __('La tabla no tiene campo de foto')], 400);
                }

                if ($request->hasFile('photo')) {
                    $file = $request->file('photo');
                    
                    // Verificación técnica de seguridad antes de procesar/validar
                    // Usamos getPathname() que es más fiable para archivos temporales que aún no se han movido
                    $tempPath = $file->getPathname();
                    
                    if (!$file || !$file->isValid() || empty($tempPath)) {
                        $errorMsg = __('El archivo no es válido.');
                        if ($file) {
                            $errorMsg .= ' Error: ' . $file->getErrorMessage() . ' (Code: ' . $file->getError() . ')';
                            if (empty($tempPath)) {
                                $errorMsg .= ' | Temp path is empty.';
                            }
                        }
                        return response()->json(['success' => false, 'message' => $errorMsg], 422);
                    }

                    $validator = Validator::make($request->all(), [
                        'photo' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
                    ]);
                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => $validator->errors()->first(),
                        ], 422);
                    }

                    $size = @getimagesize($tempPath);
                    if (!$size) {
                        return response()->json(['success' => false, 'message' => __('Imagen inválida o formato no soportado.')], 422);
                    }

                    // Eliminamos restricciones de relación de aspecto para permitir "cualquier foto"
                    // ya que el CSS con object-fit: cover se encarga de que se vea bien.

                    // --- NUEVA LÓGICA: ALMACENAMIENTO LOCAL ---
                    $relativeDir = 'fotos_de_perfil';
                    $destinationPath = public_path($relativeDir);
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    // Obtener la ruta guardada actualmente
                    $currentPath = $model->getRawOriginal('foto_url');

                    // Eliminar foto anterior si es una ruta local y el archivo existe
                    if ($currentPath && !str_starts_with($currentPath, 'http')) {
                        $oldFile = public_path(ltrim($currentPath, '/'));
                        if (file_exists($oldFile) && is_file($oldFile)) {
                            @unlink($oldFile);
                        }
                    }

                    $extension = $file->getClientOriginalExtension();
                    $fileName = 'perfil_' . $user->role . '_' . $user->id . '_' . time() . '.' . $extension;
                    
                    // Asegurarse de que el archivo temporal existe antes de moverlo
                    if (!file_exists($tempPath)) {
                        return response()->json(['success' => false, 'message' => __('El archivo temporal de la imagen ya no existe en el disco.')], 422);
                    }

                    $file->move($destinationPath, $fileName);

                    // Guardamos la ruta RELATIVA en la base de datos
                    $relativeFileUrl = $relativeDir . '/' . $fileName;
                    $model->update(['foto_url' => $relativeFileUrl]);

                    // El accesor en el modelo se encargará de devolver la URL completa con asset()
                    $fullUrl = asset($relativeFileUrl);

                    return response()->json([
                        'success' => true,
                        'message' => __('Foto actualizada correctamente'),
                        'foto_url' => $fullUrl,
                    ]);
                }

                if ($request->filled('foto_url')) {
                    $validator = Validator::make($request->all(), [
                        'foto_url' => 'required|url|max:500',
                    ]);
                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => $validator->errors()->first(),
                        ], 422);
                    }
                    $model->update(['foto_url' => $request->foto_url]);
                    return response()->json([
                        'success' => true,
                        'message' => __('Foto actualizada correctamente'),
                        'foto_url' => $request->foto_url,
                    ]);
                }

                return response()->json(['success' => false, 'message' => __('No se recibió ninguna imagen')], 422);
            }

            return response()->json(['success' => false, 'message' => __('No se encontró el perfil')], 404);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Error al actualizar foto: ') . $e->getMessage()], 500);
        }
    }

    public function dashboard($id = null)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Admins y Tutores pueden ver cualquier perfil por su ID
        if ($user->role === 'admin' || $user->role === 'tutor') {
            if ($id === null) {
                return ($user->role === 'admin') 
                    ? redirect()->route('alumnos.index') 
                    : redirect()->route('tutor.dashboard', ['id' => $user->tutor->idTutores]);
            }
            $alumno = Alumno::with(['historialAcademico', 'grupo', 'tutor', 'carreras'])->where('idAlumnos', $id)->firstOrFail();
            return view('alumnos.alumno', compact('alumno'));
        }

        // Alumnos solo ven su propio perfil
        if ($user->role === 'alumno') {
            $ownId = $user->alumno->idAlumnos ?? null;
            if ($id !== null && (int)$id !== (int)$ownId) {
                return redirect()->route('alumno.dashboard', ['id' => $ownId]);
            }
            $alumno = Alumno::with(['historialAcademico', 'grupo', 'tutor', 'carreras'])->where('idAlumnos', $ownId ?? 0)->firstOrFail();
            return view('alumnos.alumno', compact('alumno'));
        }

        return redirect()->route('login');
    }

    public function misCitas($id)
    {
        $alumno = Alumno::with(['citasTutoria.tutor', 'tutor'])->findOrFail($id);
        return view('alumnos.alumno_citas', compact('alumno'));
    }

    public function miReporte($id)
    {
        $alumno = Alumno::with(['tutor', 'historialAcademico', 'carreras', 'grupo'])->findOrFail($id);
        
        // Agrupar historial por ciclo para la vista de desempeño
        $periodos = $alumno->historialAcademico->groupBy('Ciclo')->map(function ($materias, $ciclo) {
            $avg = $materias->avg('Calificacion');
            return [
                'ciclo' => $ciclo,
                'promedio' => number_format($avg, 1),
                'materias_count' => $materias->count(),
                'riesgo' => Alumno::getRiesgoStatus($avg),
                'materias' => $materias
            ];
        });

        return view('alumnos.alumno_reporte', compact('alumno', 'periodos'));
    }

    public function misPsicologias($id)
    {
        $alumno = Alumno::with(['citasPsicologia.tutor', 'tutor'])->findOrFail($id);
        return view('alumnos.alumno_psicologia', compact('alumno'));
    }

    public function misAsesorias($id)
    {
        $alumno = Alumno::with(['asesorias', 'tutor'])->findOrFail($id);
        return view('alumnos.alumno_asesorias', compact('alumno'));
    }

    public function imprimirExpediente($id)
    {
        $alumno = Alumno::with([
            'carreras', 
            'grupo', 
            'tutor', 
            'historialAcademico', 
            'citasTutoria.tutor', 
            'citasPsicologia.tutor', 
            'asesorias'
        ])->findOrFail($id);
        
        return view('alumnos.expediente_print', compact('alumno'));
    }

    public function imprimirDesempeno($id)
    {
        $alumno = Alumno::with(['historialAcademico', 'carreras', 'grupo', 'tutor'])->findOrFail($id);
        return view('alumnos.pdf_desempeno_print', compact('alumno'));
    }

    public function imprimirDesempenoPeriodo($id, $periodo)
    {
        $alumno = Alumno::with(['historialAcademico', 'carreras', 'grupo', 'tutor'])->findOrFail($id);
        
        // Filtrar historial por el periodo específico
        $historialFiltrado = $alumno->historialAcademico->where('Ciclo', $periodo);
        
        if ($historialFiltrado->isEmpty()) {
            return redirect()->back()->with('error', 'No hay datos para el periodo seleccionado.');
        }

        return view('alumnos.pdf_desempeno_periodo_print', compact('alumno', 'historialFiltrado', 'periodo'));
    }

    public function imprimirPsicologia($id)
    {
        $alumno = Alumno::with(['citasPsicologia.tutor', 'carreras', 'tutor'])->findOrFail($id);
        return view('alumnos.pdf_psicologia_print', compact('alumno'));
    }

    public function imprimirAsesorias($id)
    {
        $alumno = Alumno::with(['asesorias', 'carreras', 'tutor'])->findOrFail($id);
        return view('alumnos.pdf_asesorias_print', compact('alumno'));
    }

    public function imprimirCitaTutoria($id, $citaId)
    {
        $alumno = Alumno::with(['carreras', 'tutor'])->findOrFail($id);
        $cita = CitaTutoria::with('tutor')->findOrFail($citaId);
        return view('alumnos.pdf_cita_tutoria_print', compact('alumno', 'cita'));
    }

    public function imprimirCitaPsicologia($id, $citaId)
    {
        $alumno = Alumno::with(['carreras', 'tutor'])->findOrFail($id);
        $cita = CitaPsicologia::with('tutor')->findOrFail($citaId);
        return view('alumnos.pdf_cita_psicologia_print', compact('alumno', 'cita'));
    }

    public function imprimirAsesoria($id, $asesoriaId)
    {
        $alumno = Alumno::with(['carreras', 'tutor'])->findOrFail($id);
        $asesoria = Asesoria::findOrFail($asesoriaId);
        return view('alumnos.pdf_asesoria_item_print', compact('alumno', 'asesoria'));
    }

    public function imprimirResumen($id)
    {
        $alumno = Alumno::with([
            'carreras', 
            'grupo', 
            'tutor', 
            'historialAcademico', 
            'citasTutoria.tutor', 
            'citasPsicologia.tutor', 
            'asesorias'
        ])->findOrFail($id);
        
        return view('alumnos.pdf_resumen_print', compact('alumno'));
    }

    public function updateEstatus(Request $request, $id)
    {
        $request->validate([
            'estatus' => 'required|string|in:activo,baja,riesgo'
        ]);

        $alumno = Alumno::findOrFail($id);
        $alumno->Estatus = $request->estatus;
        $alumno->save();

        return redirect()->back()->with('success', __('Estatus del alumno actualizado correctamente.'));
    }
}
