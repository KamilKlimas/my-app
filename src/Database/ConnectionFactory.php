<?php

declare(strict_types=1);

namespace App\Database;

final class ConnectionFactory
{
    public function create(DatabaseConfig $config): \PDO
    {
        try {
            return new \PDO($config->dsn(), $config->user, $config->password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());

            throw new ConnectionFailed();
        }
    }
}
