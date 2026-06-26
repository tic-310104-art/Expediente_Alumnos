<?php

namespace App\Utils;

use App\Services\TokenService;

class JWT
{
    /**
     * Helper estático para codificar un payload en un JWT usando el TokenService.
     * 
     * @param array $payload Datos a incluir en el token.
     * @param int $hours Horas de validez.
     * @return string
     */
    public static function encode(array $payload, int $hours = 8): string
    {
        return app(TokenService::class)->encode($payload, $hours);
    }
}
