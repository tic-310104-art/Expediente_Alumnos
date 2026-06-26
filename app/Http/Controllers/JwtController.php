<?php

namespace App\Http\Controllers;

use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JwtController extends Controller
{
    public function generate(TokenService $tokenService)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'tutor'])) {
            return response()->json(['success' => false, 'message' => __('No autorizado')], 403);
        }

        $token = $tokenService->generateToken((int) $user->id, (string) $user->role);
        $payload = $tokenService->decodeAndValidate($token);

        session(['critical_token' => $token]);

        $exp = is_array($payload) ? (int) ($payload['exp'] ?? (time() + 8 * 3600)) : (time() + 8 * 3600);

        return response()->json([
            'success' => true,
            'token' => $token,
            'expires_at' => date('h:i A', $exp),
        ]);
    }

    public function verify(Request $request, TokenService $tokenService)
    {
        $token = (string) $request->input('token', '');
        
        // Si no se envió token manual, intentamos usar el de la sesión
        if ($token === '') {
            $token = (string) session('critical_token', '');
        }

        if ($token === '') {
            return response()->json(['success' => false, 'message' => __('Seguridad: Se requiere autorización para esta acción')], 401);
        }

        $payload = $tokenService->decodeAndValidate($token);
        if (!$payload) {
            return response()->json(['success' => false, 'message' => __('Token inválido o expirado')], 401);
        }

        session(['critical_token' => $token]);

        return response()->json([
            'success' => true,
            'message' => __('Token válido'),
            'user_id' => $payload['user_id'] ?? null,
            'role' => $payload['role'] ?? null,
            'iat' => $payload['iat'] ?? null,
            'exp' => $payload['exp'] ?? null,
        ]);
    }
}

