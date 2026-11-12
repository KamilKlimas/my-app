<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\BadRequest;
use App\Http\Query;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testStringReturnsGivenValue(): void
    {
        $query = new Query(['map' => 'de_mirage']);

        $this->assertSame('de_mirage', $query->string('map', 'all'));
    }

    public function testStringReturnsDefaultWhenParameterIsMissing(): void
    {
        $query = new Query([]);

        $this->assertSame('all', $query->string('map', 'all'));
    }

    public function testStringKeepsEmptyValueInsteadOfDefault(): void
    {
        $query = new Query(['map' => '']);

        $this->assertSame('', $query->string('map', 'all'));
    }

    public function testStringRejectsArrayValue(): void
    {
        $query = new Query(['map' => ['x']]);

        $this->expectException(BadRequest::class);
        $query->string('map', 'all');
    }
}
