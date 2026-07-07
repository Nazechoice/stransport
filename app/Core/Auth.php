<?php

declare(strict_types=1);

namespace Transport\Core;

use Transport\Models\User;

final class Auth
{
    public static function user(): ?array
    {
        static $resolved = false;
        static $cachedUser = null;

        if ($resolved) {
            return $cachedUser;
        }

        $resolved = true;
        $sessionUser = Session::get('auth_user');
        if (!$sessionUser || empty($sessionUser['id'])) {
            return $cachedUser = null;
        }

        $user = (new User())->find((int) $sessionUser['id']);
        if (!$user || ($user['status'] ?? null) !== 'active') {
            Session::destroy();
            return $cachedUser = null;
        }

        $cachedUser = [
            'id' => (int) $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
        ];

        Session::set('auth_user', $cachedUser);

        return $cachedUser;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        Session::set('auth_user', [
            'id' => (int) $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
        ]);
        Session::remove('_old_input');
    }

    public static function logout(): void
    {
        $user = self::user();
        if ($user && !empty($user['id'])) {
            try {
                (new User())->setRememberToken((int) $user['id'], null);
            } catch (\Throwable) {
            }
        }

        Session::destroy();
    }

    public static function bootRememberMe(): void
    {
        if (self::check()) {
            Session::touchActivity();
            return;
        }

        $cookieKey = config('security.remember_cookie');
        if (empty($_COOKIE[$cookieKey]) || !str_contains($_COOKIE[$cookieKey], '|')) {
            return;
        }

        [$userId, $token] = explode('|', $_COOKIE[$cookieKey], 2);
        $user = (new User())->find((int) $userId);
        if ($user && ($user['status'] ?? null) === 'active' && hash_equals((string) ($user['remember_token'] ?? ''), $token)) {
            self::login($user);
            Session::touchActivity();
            return;
        }

        setcookie(
            $cookieKey,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public static function requireRole(array|string $roles): void
    {
        $roles = (array) $roles;
        $user = self::user();
        if (!$user || !in_array($user['role'], $roles, true)) {
            Response::redirect('login');
        }
    }
}
