<?php

declare(strict_types=1);

namespace Transport\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = require __DIR__ . '/../../config/config.php';
        session_name($config['app']['session_name']);
        ini_set('session.gc_maxlifetime', (string) $config['app']['session_lifetime']);
        session_set_cookie_params([
            'lifetime' => $config['app']['session_lifetime'],
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
        self::touchActivity();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        $old = self::get('_old_input', []);
        return $old[$key] ?? $default;
    }

    public static function flashOld(array $input): void
    {
        self::set('_old_input', $input);
    }

    public static function clearOld(): void
    {
        self::remove('_old_input');
    }

    public static function touchActivity(): void
    {
        $_SESSION['_last_activity'] = time();
    }

    public static function isTimedOut(int $lifetime): bool
    {
        $last = $_SESSION['_last_activity'] ?? time();
        return (time() - (int) $last) > $lifetime;
    }
}
