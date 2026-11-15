<?php

declare(strict_types=1);

namespace App\Http;

final class Session
{
    private const COOKIE_NAME = 'duellab_session';

    /** @param array<string, mixed> $data */
    public function __construct(private array &$data)
    {
    }

    public static function start(bool $secure): self
    {
        $started = session_start([
            'name' => self::COOKIE_NAME,
            'use_strict_mode' => 1,
            'use_only_cookies' => 1,
            'cookie_lifetime' => 0,
            'cookie_path' => '/',
            'cookie_httponly' => 1,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => $secure ? 1 : 0,
        ]);

        if (!$started) {
            throw new \RuntimeException('Session could not be started');
        }

        return new self($_SESSION);
    }

    /** @param array<string, string> $env */
    public static function secureFromEnvironment(array $env): bool
    {
        return match ($env['SESSION_SECURE'] ?? '') {
            '1' => true,
            '0' => false,
            default => throw new \RuntimeException('SESSION_SECURE must be 0 or 1'),
        };
    }

    public function string(string $key): ?string
    {
        $value = $this->data[$key] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new \UnexpectedValueException('Session value is not a string: ' . $key);
        }

        return $value;
    }

    public function int(string $key): ?int
    {
        $value = $this->data[$key] ?? null;

        if ($value !== null && !is_int($value)) {
            throw new \UnexpectedValueException('Session value is not an int: ' . $key);
        }

        return $value;
    }

    public function regenerate(): void
    {
        if (!session_regenerate_id(true)) {
            throw new \RuntimeException('Session id could not be regenerated');
        }
    }

    public function destroy(): void
    {
        $this->data = [];
        $cookie = session_get_cookie_params();
        unset($cookie['lifetime']);
        setcookie(session_name(), '', ['expires' => time() - 3600] + $cookie);
        session_destroy();
    }

    public function set(string $key, string|int $value): void
    {
        $this->data[$key] = $value;
    }
}
