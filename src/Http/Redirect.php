<?php

declare(strict_types=1);

namespace App\Http;

final class Redirect extends \RuntimeException
{
    public function __construct(private readonly string $path)
    {
        if (!preg_match('~^/(?![/\\\\])~', $path)) {
            throw new \InvalidArgumentException('Redirect target must be a local path: ' . $path);
        }

        parent::__construct('Redirect to ' . $path);
    }

    public function path(): string
    {
        return $this->path;
    }
}
