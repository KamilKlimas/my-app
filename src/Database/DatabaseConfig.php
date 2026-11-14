<?php

declare(strict_types=1);

namespace App\Database;

final readonly class DatabaseConfig
{
    private function __construct(
        public string $host,
        public string $port,
        public string $name,
        public string $user,
        #[\SensitiveParameter] public string $password,
    ) {
    }

    /** @param array<string, string> $env */
    public static function fromEnvironment(array $env): self
    {
        return new self(
            self::required($env, 'DB_HOST'),
            self::required($env, 'DB_PORT'),
            self::required($env, 'DB_NAME'),
            self::required($env, 'DB_USER'),
            self::required($env, 'DB_PASSWORD'),
        );
    }

    public function dsn(): string
    {
        return sprintf('pgsql:host=%s;port=%s;dbname=%s', $this->host, $this->port, $this->name);
    }

    /** @param array<string, string> $env */
    private static function required(array $env, string $name): string
    {
        $value = $env[$name] ?? '';

        if ($value === '') {
            throw new \RuntimeException('Missing environment variable ' . $name);
        }

        return $value;
    }
}
