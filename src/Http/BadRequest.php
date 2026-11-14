<?php

declare(strict_types=1);

namespace App\Http;

final class BadRequest extends HttpException
{
    public function statusCode(): int
    {
        return 400;
    }
}
