<?php

declare(strict_types=1);

namespace App\Http;

final class Redirect extends \RuntimeException
{
    public function __construct(private readonly string $path)
    {
        if (!self::isLocalPath($path)) {
            throw new \InvalidArgumentException('Redirect target must be a local path: ' . $path);
        }

        parent::__construct('Redirect to ' . $path);
    }

    public static function isLocalPath(string $path): bool
    {
        return preg_match('~^/(?![/\\\\])~', $path) === 1;
    }

    public function path(): string
    {
        return $this->path;
    }
}
