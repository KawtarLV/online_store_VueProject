<?php

namespace App\Utils;

class JwtManager
{
    private const ALGO = 'sha256';

    /**
     * @param array<string, mixed> $payload
     */
    public static function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $secret = self::secret();

        $segments = [
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        ];

        $signature = hash_hmac(self::ALGO, implode('.', $segments), $secret, true);
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;
        $expected = self::base64UrlEncode(
            hash_hmac(self::ALGO, $encodedHeader . '.' . $encodedPayload, self::secret(), true)
        );

        if (!hash_equals($expected, $encodedSignature)) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($encodedPayload), true);
        if (!is_array($payload)) {
            return null;
        }

        $expiresAt = isset($payload['exp']) ? (int) $payload['exp'] : 0;
        if ($expiresAt > 0 && $expiresAt < time()) {
            return null;
        }

        return $payload;
    }

    private static function secret(): string
    {
        return getenv('JWT_SECRET') ?: 'change-this-secret';
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        return base64_decode(strtr($value, '-_', '+/')) ?: '';
    }
}
