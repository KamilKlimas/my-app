<?php

declare(strict_types=1);

namespace App\Http;

final class Query
{
    public function __construct(private readonly array $parameters)
    {
    }

    public function string(string $name, string $default): string
    {
        $value = $this->parameters[$name] ?? $default;

        if (!is_string($value)) {
            throw new BadRequest('Query parameter must be a string: ' . $name);
        }

        return $value;
    }
}
