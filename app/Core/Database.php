<?php

declare(strict_types=1);

namespace Transport\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    public static function pdo(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $config = require __DIR__ . '/../../config/config.php';
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['db']['driver'],
            $config['db']['host'],
            $config['db']['port'],
            $config['db']['database'],
            $config['db']['charset']
        );

        self::$instance = new PDO($dsn, $config['db']['username'], $config['db']['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$instance;
    }
}

