<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\AsesoriaController;
use App\Http\Controllers\CitaPsicologiaController;
use App\Http\Controllers\CitaTutoriaController;
use App\Http\Controllers\HistorialAcademicoController;
use App\Http\Controllers\ServicioEscolarController;
use App\Http\Controllers\ReporteDesempenoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BecaController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\JwtController;
use App\Http\Controllers\CalificacionController;

// --- RUTAS PÚBLICAS ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

// --- RUTAS DE AUTENTICACIÓN ---
// Solo mostrar el formulario requiere no estar autenticado
Route::middleware('guest')->get('/sesion', [AuthController::class, 'showLogin']);

// El POST de login siempre se procesa (el método redirige si ya está autenticado)
Route::post('/sesion', [AuthController::class, 'login'])->name('login.post');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTAS PROTEGIDAS POR AUTENTICACIÓN ---
Route::middleware('auth')->group(function () {

    Route::post('/perfil/foto', [AlumnoController::class, 'updateFoto'])->name('perfil.foto.update');
    Route::delete('/perfil/foto', [AlumnoController::class, 'deleteFoto'])->name('perfil.foto.delete');

    Route::get('/set-locale/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'es'])) {
            session(['locale' => $locale]);
        }
        return back();
    })->name('set-locale');

    Route::post('/verify-critical-action', [AuthController::class, 'verifyCriticalAction'])->name('critical.verify');

    // JWT para acciones críticas (disponible para admin y tutor)
    Route::get('/jwt/generate', [JwtController::class, 'generate'])->name('jwt.generate');
    Route::post('/jwt/verify', [JwtController::class, 'verify'])->name('jwt.verify');

    // Rutas que requieren confirmación crítica (Admin y Tutor)
    Route::middleware(['role:tutor', 'critical'])->group(function() {
        Route::post('/alumnos/{alumno}/estatus', [AlumnoController::class, 'updateEstatus'])->name('alumnos.estatus');
    });

    // APIs para filtros dinámicos
    Route::get('/api/carreras/{carrera}/grupos', function($carreraId) {
        return \App\Models\Grupo::where('idCarreras', $carreraId)->get();
    });
    Route::get('/api/grupos/{grupo}', function($grupoId) {
        return \App\Models\Grupo::with(['tutor', 'carrera'])->find($grupoId);
    });

    // === ROL: SERVICIOS ESCOLARES (ADMIN) ===
    Route::middleware('role:admin')->group(function () {
        Route::get('/expedienteGeneral', [ServicioEscolarController::class, 'dashboard'])->name('expedienteGeneral');
        
        // Rutas que requieren confirmación crítica
        Route::middleware('critical')->group(function() {
            Route::post('/backup/schedule', [ServicioEscolarController::class, 'scheduleBackup'])->name('backup.schedule');
            Route::post('/backup/import', [ServicioEscolarController::class, 'importBackup'])->name('backup.import');
            Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');
            Route::delete('/tutores/{tutore}', [TutorController::class, 'destroy'])->name('tutores.destroy');
            Route::delete('/servicios/{servicio}', [ServicioEscolarController::class, 'destroy'])->name('servicios.destroy');
            Route::delete('/carreras/{carrera}', [CarreraController::class, 'destroy'])->name('carreras.destroy');
            Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy');
            Route::delete('/materias/{materia}', [MateriaController::class, 'destroy'])->name('materias.destroy');
            
            Route::match(['PUT', 'PATCH'], '/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
            Route::match(['PUT', 'PATCH'], '/tutores/{tutore}', [TutorController::class, 'update'])->name('tutores.update');
            Route::match(['PUT', 'PATCH'], '/servicios/{servicio}', [ServicioEscolarController::class, 'update'])->name('servicios.update');
            Route::match(['PUT', 'PATCH'], '/carreras/{carrera}', [CarreraController::class, 'update'])->name('carreras.update');
            Route::match(['PUT', 'PATCH'], '/grupos/{grupo}', [GrupoController::class, 'update'])->name('grupos.update');
            Route::match(['PUT', 'PATCH'], '/materias/{materia}', [MateriaController::class, 'update'])->name('materias.update');
            Route::match(['PUT', 'PATCH'], '/becas/{beca}', [BecaController::class, 'update'])->name('becas.update');

            Route::post('/tutores/assign-grupo', [TutorController::class, 'assignGrupo'])->name('tutores.assign');
            Route::post('/grupos/{grupo}/carga', [GrupoController::class, 'storeCarga'])->name('grupos.carga.store');
            
            Route::delete('/becas/{beca}', [BecaController::class, 'destroy'])->name('becas.destroy');
            Route::delete('/becas/{beca}/unassign/{alumno}', [BecaController::class, 'unassign'])->name('becas.unassign');
        });

        Route::resource('alumnos', AlumnoController::class)->except(['destroy', 'update']);
        Route::resource('tutores', TutorController::class)->except(['destroy', 'update']);
        Route::resource('carreras', CarreraController::class)->except(['destroy', 'update']);
        Route::resource('grupos', GrupoController::class)->except(['destroy', 'update']);
        Route::get('/grupos/{grupo}/carga', [GrupoController::class, 'manageCarga'])->name('grupos.carga');
        Route::resource('servicios', ServicioEscolarController::class)->except(['destroy', 'update']);
        Route::resource('becas', BecaController::class)->except(['destroy', 'update']);
        Route::post('/becas/assign', [BecaController::class, 'assign'])->name('becas.assign');
        Route::resource('materias', MateriaController::class)->only(['show', 'store']);
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
        Route::get('/logs/print', [LogController::class, 'print'])->name('logs.print');
    });

    // === ROL: TUTOR ===
    Route::middleware('role:tutor')->group(function () {
        Route::prefix('tutor')->name('tutor.')->group(function () {
            Route::get('/dashboard/{id?}', [TutorController::class, 'dashboard'])->name('dashboard');

            // Rutas con {id} protegidas: solo el tutor propietario puede acceder
            Route::middleware('ownership:tutores,idTutores')->group(function () {
                Route::get('/{id}/citas',      [TutorController::class, 'misCitas'])->name('citas');
                Route::get('/{id}/psicologia', [TutorController::class, 'misPsicologias'])->name('psicologia');
                Route::get('/{id}/asesorias',  [TutorController::class, 'misAsesorias'])->name('asesorias');

                // Rutas de calificaciones
                Route::get('/{id}/alumnos/{alumnoId}/calificaciones', [CalificacionController::class, 'asignarCalificaciones'])->name('alumnos.calificaciones');
                Route::post('/{id}/alumnos/{alumnoId}/calificaciones', [CalificacionController::class, 'guardarCalificaciones'])->name('alumnos.calificaciones.guardar');
            });
        });

        Route::resource('asesorias', AsesoriaController::class);
        Route::resource('citas-tutoria', CitaTutoriaController::class)->except(['index', 'create', 'show']);
        Route::resource('citas-psicologia', CitaPsicologiaController::class);
        Route::resource('reportes-desempeno', ReporteDesempenoController::class)->only(['show', 'store', 'update', 'destroy']);
        Route::resource('historial', HistorialAcademicoController::class)->only(['show', 'store', 'update', 'destroy']);
    });

    // === ACCESO A DATOS DE ALUMNO (Accesible por Alumno, su Tutor o Admin) ===
    Route::prefix('alumno')->name('alumno.')->middleware('ownership:alumnos,idAlumnos')->group(function () {
        Route::get('/{id?}', [AlumnoController::class, 'dashboard'])->name('dashboard');
        Route::get('/{id}/citas',          [AlumnoController::class, 'misCitas'])->name('citas');
        Route::get('/{id}/reporte',        [AlumnoController::class, 'miReporte'])->name('reporte');
        Route::get('/{id}/psicologia',     [AlumnoController::class, 'misPsicologias'])->name('psicologia');
        Route::get('/{id}/asesorias',      [AlumnoController::class, 'misAsesorias'])->name('asesorias');
        Route::get('/{id}/expediente-pdf', [AlumnoController::class, 'imprimirExpediente'])->name('expediente.pdf');
        Route::get('/{id}/historial',     [CalificacionController::class, 'verHistorial'])->name('historial');
        Route::get('/{id}/pdf/desempeno',          [AlumnoController::class, 'imprimirDesempeno'])->name('pdf.desempeno');
        Route::get('/{id}/pdf/desempeno/{periodo}', [AlumnoController::class, 'imprimirDesempenoPeriodo'])->name('pdf.desempeno.periodo');
        Route::get('/{id}/pdf/psicologia',         [AlumnoController::class, 'imprimirPsicologia'])->name('pdf.psicologia');
        Route::get('/{id}/pdf/asesorias',  [AlumnoController::class, 'imprimirAsesorias'])->name('pdf.asesorias');
        Route::get('/{id}/pdf/citas-tutoria/{cita}', [AlumnoController::class, 'imprimirCitaTutoria'])->name('pdf.citas_tutoria.item');
        Route::get('/{id}/pdf/psicologia/{cita}', [AlumnoController::class, 'imprimirCitaPsicologia'])->name('pdf.psicologia.item');
        Route::get('/{id}/pdf/asesorias/{asesoria}', [AlumnoController::class, 'imprimirAsesoria'])->name('pdf.asesorias.item');
        Route::get('/{id}/pdf/resumen', [AlumnoController::class, 'imprimirResumen'])->name('pdf.resumen');
    });

    // === ROL: ALUMNO ===
    Route::middleware('role:alumno')->group(function () {
        Route::get('/mi-historial/{id}', [HistorialAcademicoController::class, 'show'])->name('historial.personal');
    });
});
