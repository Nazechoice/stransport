<?php

declare(strict_types=1);

use Transport\Core\Flash;
use Transport\Core\Request;
use Transport\Core\Session;
use Transport\Core\Url;
use Transport\Models\Setting;
use Transport\Core\Auth;

if (!function_exists('config')) {
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $config;

        if ($config === null) {
            $config = require __DIR__ . '/../../config/config.php';
        }

        if ($key === null) {
            return $config;
        }

        $segments = explode('.', $key);
        $value = $config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return Url::to($path);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return Url::asset($path);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::old($key, $default);
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null): mixed
    {
        if ($value === null) {
            return Flash::get($key);
        }

        Flash::set($key, $value);
        return null;
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('money')) {
    function money(float|int|string $amount): string
    {
        return html_entity_decode('&#8358;', ENT_QUOTES, 'UTF-8') . number_format((float) $amount, 2);
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('auth_role')) {
    function auth_role(): ?string
    {
        return auth_user()['role'] ?? null;
    }
}

if (!function_exists('validation_errors')) {
    function validation_errors(): array
    {
        $errors = Session::get('errors', []);
        if ($errors) {
            Session::remove('errors');
        }
        return is_array($errors) ? $errors : [];
    }
}

if (!function_exists('request_path')) {
    function request_path(): string
    {
        return Request::uri();
    }
}

if (!function_exists('route_is')) {
    function route_is(string|array $paths): bool
    {
        $currentPath = request_path();

        foreach ((array) $paths as $path) {
            $path = '/' . ltrim(trim((string) $path), '/');

            if ($path === '/' && $currentPath === '/') {
                return true;
            }

            if ($path !== '/' && (str_starts_with($currentPath, $path) || $currentPath === rtrim($path, '/'))) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('status_badge_class')) {
    function status_badge_class(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match (true) {
            in_array($status, ['active', 'confirmed', 'completed', 'paid', 'read', 'scheduled'], true) => 'text-bg-success',
            in_array($status, ['pending', 'processing', 'boarding', 'maintenance'], true) => 'text-bg-warning',
            in_array($status, ['info', 'verified', 'assigned'], true) => 'text-bg-info',
            in_array($status, ['inactive', 'cancelled', 'closed', 'failed', 'suspended', 'deleted', 'expired', 'unpaid'], true) => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }
}

if (!function_exists('role_badge_class')) {
    function role_badge_class(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        return match ($role) {
            'super_admin' => 'text-bg-dark',
            'administrator' => 'text-bg-primary',
            'ticket_officer' => 'text-bg-info',
            'driver' => 'text-bg-success',
            'passenger' => 'text-bg-secondary',
            default => 'text-bg-secondary',
        };
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return auth_user() !== null;
    }
}

if (!function_exists('system_setting')) {
    function system_setting(string $key, mixed $default = null): mixed
    {
        static $cache = null;

        if ($cache === null) {
            $cache = (new Setting())->allKeyValue();
        }

        return $cache[$key] ?? $default;
    }
}
