<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\BadRequest;
use App\Http\Forbidden;
use App\Http\HttpException;
use App\Http\NotFound;
use App\Http\ServiceUnavailable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HttpExceptionTest extends TestCase
{
    public static function exceptionsWithStatus(): array
    {
        return [
            'bad request' => [new BadRequest(), 400],
            'forbidden' => [new Forbidden(), 403],
            'not found' => [new NotFound(), 404],
            'service unavailable' => [new ServiceUnavailable(), 503],
        ];
    }

    #[DataProvider('exceptionsWithStatus')]
    public function testExceptionCarriesItsStatusCode(HttpException $exception, int $statusCode): void
    {
        $this->assertSame($statusCode, $exception->statusCode());
    }
}
