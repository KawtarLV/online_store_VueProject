<?php

namespace App\Framework;

use PDO;

/**
 * Lightweight PDO singleton for MySQL connections.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $host = getenv('DB_HOST') ?: 'mysql';
        $db   = getenv('DB_NAME') ?: 'ecommerce_store';
        $user = getenv('DB_USER') ?: 'developer';
        $pass = getenv('DB_PASS') ?: 'secret123';

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $db);

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }
}
