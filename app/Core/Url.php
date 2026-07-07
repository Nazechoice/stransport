<?php

declare(strict_types=1);

namespace Transport\Core;

final class Url
{
    public static function base(): string
    {
        $config = require __DIR__ . '/../../config/config.php';
        return rtrim($config['app']['base_url'], '/');
    }

    public static function to(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $base = self::base();
        return $base === '' ? '/' . $path : $base . '/' . $path;
    }

    public static function asset(string $path): string
    {
        return self::to('public/assets/' . ltrim($path, '/'));
    }
}
