<?php

use App\Http\Controllers\ApiCalificacionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoints consumidos por la aplicación móvil (Android). Reutilizan la
| sesión web que establece la app mediante el login de /sesion, por eso
| llevan el middleware 'web' (StartSession) además de 'auth'.
|
*/

Route::middleware(['web', 'auth'])->group(function () {

    // Listado de materias del catálogo (nombre precargado para la app)
    Route::get('/materias', [ApiCalificacionController::class, 'materias']);

    // Carga académica de un alumno (materias del grupo + calificación actual)
    Route::get('/carga', [ApiCalificacionController::class, 'carga']);

    // Guardar / actualizar calificaciones de un alumno
    Route::post('/calificaciones', [ApiCalificacionController::class, 'guardar']);
});
