<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class TokenService
{
    public function generateToken(int $userId, string $role): string
    {
        return $this->encode([
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function encode(array $payload, int $hours = 8): string
    {
        $iat = time();
        $exp = $iat + ($hours * 3600);

        if (!isset($payload['iat'])) $payload['iat'] = $iat;
        if (!isset($payload['exp'])) $payload['exp'] = $exp;

        $header = ['typ' => 'JWT', 'alg' => 'HS256'];

        $encodedHeader = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signature = $this->sign($encodedHeader . '.' . $encodedPayload);
        $encodedSignature = $this->base64UrlEncode($signature);

        return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
    }

    public function decodeAndValidate(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $expected = $this->base64UrlEncode($this->sign($encodedHeader . '.' . $encodedPayload));
        if (!hash_equals($expected, $encodedSignature)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecodeToString($encodedPayload);
        if ($payloadJson === null) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }

        $exp = $payload['exp'] ?? null;
        if (!is_int($exp) && !ctype_digit((string) $exp)) {
            return null;
        }

        if ((int) $exp < time()) {
            return null;
        }

        return $payload;
    }

    private function sign(string $data): string
    {
        return hash_hmac('sha256', $data, $this->secretKey(), true);
    }

    private function secretKey(): string
    {
        $key = (string) Config::get('app.key', '');
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded !== false) {
                return $decoded;
            }
        }
        return $key;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecodeToString(string $data): ?string
    {
        $data = strtr($data, '-_', '+/');
        $padding = strlen($data) % 4;
        if ($padding > 0) {
            $data .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($data, true);
        return $decoded === false ? null : $decoded;
    }
}

