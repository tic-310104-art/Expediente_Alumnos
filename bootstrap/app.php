<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(prepend: [
            \App\Http\Middleware\EmergencyAuthMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\LocaleMiddleware::class,
            \App\Http\Middleware\LogActivityMiddleware::class,
        ]);

        $middleware->alias([
            'role'      => \App\Http\Middleware\RoleMiddleware::class,
            'ownership' => \App\Http\Middleware\EnsureOwnership::class,
            'critical'  => \App\Http\Middleware\CriticalActionMiddleware::class,
            'jwt'       => \App\Http\Middleware\EnsureJwtIsValid::class,
        ]);

        // Cuando un usuario autenticado intenta acceder a rutas de 'guest'
        // (como /sesion), los mandamos a '/', el cual ahora maneja la
        // redirección automática a su dashboard según su rol.
        $middleware->redirectUsersTo('/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
