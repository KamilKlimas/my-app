<?php

declare(strict_types=1);

namespace App\Http;

final class ServiceUnavailable extends HttpException
{
    public function statusCode(): int
    {
        return 503;
    }
}
