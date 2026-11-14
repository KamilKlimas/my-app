<?php

declare(strict_types=1);

namespace App\Tests\Unit\Database;

use App\Database\DatabaseConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DatabaseConfigTest extends TestCase
{
    private const ENVIRONMENT = [
        'DB_HOST' => 'db',
        'DB_PORT' => '5432',
        'DB_NAME' => 'duellab',
        'DB_USER' => 'duellab',
        'DB_PASSWORD' => 'secret-password',
    ];

    public static function requiredVariables(): array
    {
        return [
            'host' => ['DB_HOST'],
            'port' => ['DB_PORT'],
            'name' => ['DB_NAME'],
            'user' => ['DB_USER'],
            'password' => ['DB_PASSWORD'],
        ];
    }

    public function testCompleteEnvironmentBuildsPostgresDsn(): void
    {
        $config = DatabaseConfig::fromEnvironment(self::ENVIRONMENT);

        $this->assertSame('pgsql:host=db;port=5432;dbname=duellab', $config->dsn());
    }

    public function testCompleteEnvironmentKeepsCredentialsOutOfDsn(): void
    {
        $config = DatabaseConfig::fromEnvironment(self::ENVIRONMENT);

        $this->assertSame('duellab', $config->user);
        $this->assertSame('secret-password', $config->password);
        $this->assertStringNotContainsString('secret-password', $config->dsn());
    }

    #[DataProvider('requiredVariables')]
    public function testMissingVariableIsRejectedByName(string $name): void
    {
        $environment = self::ENVIRONMENT;
        unset($environment[$name]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($name);

        DatabaseConfig::fromEnvironment($environment);
    }

    #[DataProvider('requiredVariables')]
    public function testEmptyVariableIsRejectedLikeMissingOne(string $name): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($name);

        DatabaseConfig::fromEnvironment([$name => ''] + self::ENVIRONMENT);
    }

    public function testEmptyPasswordErrorDoesNotLeakOtherValues(): void
    {
        try {
            DatabaseConfig::fromEnvironment(['DB_PASSWORD' => ''] + self::ENVIRONMENT);
            $this->fail('Expected a missing variable error');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Missing environment variable DB_PASSWORD', $exception->getMessage());
        }
    }
}
