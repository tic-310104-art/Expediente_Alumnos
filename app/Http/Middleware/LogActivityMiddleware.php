<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class LogActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $method = strtoupper((string) ($request->input('_method') ?: $request->method()));
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                $subject = $this->getSubject($request);
                if ($subject) {
                    $details = $this->getDetails($request);
                    
                    LogActivity::create([
                        'subject' => $subject,
                        'details' => $details,
                        'url' => $request->fullUrl(),
                        'method' => $method,
                        'ip' => $request->ip(),
                        'agent' => $request->header('user-agent'),
                        'user_id' => Auth::check() ? Auth::id() : null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silencioso para evitar romper la aplicación si falla el log
        }

        return $response;
    }

    private function getSubject(Request $request)
    {
        if ($request->routeIs('login.post')) return 'Inicio de sesión';
        if ($request->routeIs('register.post')) return 'Nuevo registro de usuario';
        if ($request->routeIs('jwt.verify')) return 'Uso de Token JWT';
        
        $method = strtoupper((string) ($request->input('_method') ?: $request->method()));
        if ($method === 'DELETE') return 'Baja (Eliminación)';
        if ($method === 'PUT' || $method === 'PATCH') return 'Edición';
        if ($method === 'POST') return 'Alta (Creación)';
        
        return null;
    }

    private function getDetails(Request $request)
    {
        $data = $request->except([
            '_token', 
            '_method', 
            'password', 
            'Password', 
            'password_confirmation', 
            'Password_confirmation',
            'current_password',
            'new_password',
            'token',
            'swal-token'
        ]);
        
        // Si es una verificación de token, registrar que se usó un token
        if ($request->routeIs('jwt.verify')) {
            return 'Se validó un token de seguridad para realizar una acción crítica.';
        }

        if (empty($data)) {
            return 'Sin datos adicionales.';
        }

        // Crear una descripción breve basada en los campos enviados
        $details = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $count = count($value);
                $details[] = "$key ($count elementos)";
            } elseif (is_string($value) || is_numeric($value)) {
                $valStr = (string)$value;
                if (strlen($valStr) > 50) $valStr = substr($valStr, 0, 47) . '...';
                $details[] = "$key: $valStr";
            }
        }

        return implode(' | ', $details);
    }
}
