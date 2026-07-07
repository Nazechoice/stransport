<?php

declare(strict_types=1);

namespace Transport\Core;

final class Flash
{
    public static function set(string $key, string $message): void
    {
        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][$key] = $message;
    }

    public static function get(string $key): ?string
    {
        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }

        $message = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $message;
    }
}
