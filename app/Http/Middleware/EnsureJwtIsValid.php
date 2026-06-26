<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJwtIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = (string) $request->header('Authorization', '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            return response()->json(['success' => false, 'message' => __('Token requerido')], 401);
        }

        $token = trim((string) $m[1]);
        $payload = app(TokenService::class)->decodeAndValidate($token);
        if (!$payload) {
            return response()->json(['success' => false, 'message' => __('Token inválido o expirado')], 401);
        }

        return $next($request);
    }
}

