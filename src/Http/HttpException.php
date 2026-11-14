<?php

declare(strict_types=1);

namespace App\Http;

abstract class HttpException extends \RuntimeException
{
    abstract public function statusCode(): int;
}
