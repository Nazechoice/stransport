<?php

declare(strict_types=1);

namespace Transport\Core;

final class Csrf
{
    public static function token(): string
    {
        $key = config('security.csrf_key');
        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = bin2hex(random_bytes(32));
        }

        return $_SESSION[$key];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        $key = config('security.csrf_key');
        return is_string($token) && isset($_SESSION[$key]) && hash_equals($_SESSION[$key], $token);
    }
}

