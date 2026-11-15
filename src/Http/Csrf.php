<?php

declare(strict_types=1);

namespace App\Http;

final class Csrf
{
    public const FIELD_NAME = '_csrf';
    private const SESSION_KEY = 'csrf_token';

    public function __construct(private Session $session)
    {
    }

    public function ensureToken(): void
    {
        if ($this->session->string(self::SESSION_KEY) === null) {
            $this->rotate();
        }
    }

    public function rotate(): void
    {
        $this->session->set(self::SESSION_KEY, bin2hex(random_bytes(32)));
    }

    public function token(): string
    {
        $token = $this->session->string(self::SESSION_KEY);

        if ($token === null) {
            throw new \LogicException('CSRF token not initialised; call ensureToken() first');
        }

        return $token;
    }

    /** @param array<string, mixed> $post */
    public function verifyRequest(string $method, array $post): void
    {
        if ($method !== 'POST') {
            return;
        }

        $expected = $this->session->string(self::SESSION_KEY);
        $given = $post[self::FIELD_NAME] ?? null;

        if ($expected === null || !is_string($given) || !hash_equals($expected, $given)) {
            throw new Forbidden();
        }
    }
}
