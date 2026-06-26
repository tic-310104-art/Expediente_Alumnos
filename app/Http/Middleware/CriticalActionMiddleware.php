<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\TokenService;

class CriticalActionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = session('critical_token');
        
        if (!$token) {
            return $this->reject($request);
        }

        $payload = app(TokenService::class)->decodeAndValidate((string) $token);

        if (!$payload || (string) ($payload['user_id'] ?? '') !== (string) Auth::id()) {
            session()->forget('critical_token');
            return $this->reject($request);
        }

        $response = $next($request);

        // Una vez que la acción crítica se completó con éxito, 
        // eliminamos el token de la sesión para que se pida en la siguiente acción.
        session()->forget('critical_token');

        return $response;
    }

    private function reject(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => __('Token requerido o expirado')], 401);
        }

        session()->flash('critical_token_required', true);
        session()->flash('critical_intended_url', $request->fullUrl());
        session()->flash('critical_intended_method', $request->method());

        return redirect()->back();
    }
}
