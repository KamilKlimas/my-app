<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Routing;
use App\Tests\Unit\Fixture\SpyController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RoutingTest extends TestCase
{
    private Routing $routing;

    protected function setUp(): void
    {
        $this->routing = new Routing();
    }

    public function testStaticRouteCallsItsAction(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index');

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('GET', '/demos'));
    }

    public function testStaticRouteDoesNotMatchAnotherPath(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index');

        $this->expectOutputString('');
        $this->assertFalse($this->routing->dispatch('GET', '/hotspots'));
    }

    public function testGetRouteDoesNotMatchPostRequest(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index');

        $this->expectOutputString('');
        $this->assertFalse($this->routing->dispatch('POST', '/demos'));
    }

    public function testPostRouteMatchesPostRequest(): void
    {
        $this->routing->post('/demos', SpyController::class, 'index');

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('POST', '/demos'));
    }

    public function testNumericParameterIsPassedAsNamedInt(): void
    {
        $this->routing->get('/demos/{id}', SpyController::class, 'show');

        $this->expectOutputString('show:int:7');
        $this->assertTrue($this->routing->dispatch('GET', '/demos/7'));
    }

    public static function pathsNotMatchingParameterRoute(): array
    {
        return [
            'non-numeric id' => ['/demos/abc'],
            'extra segment' => ['/demos/7/extra'],
            'missing id' => ['/demos/'],
            'negative id' => ['/demos/-7'],
        ];
    }

    #[DataProvider('pathsNotMatchingParameterRoute')]
    public function testParameterRouteRejectsPath(string $path): void
    {
        $this->routing->get('/demos/{id}', SpyController::class, 'show');

        $this->expectOutputString('');
        $this->assertFalse($this->routing->dispatch('GET', $path));
    }

    public function testQueryStringIsIgnoredWhenMatching(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index');

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('GET', '/demos?map=de_mirage'));
    }

    public function testFirstMatchingRouteWins(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index');
        $this->routing->get('/demos', SpyController::class, 'other');

        $this->expectOutputString('index');
        $this->routing->dispatch('GET', '/demos');
    }

    public function testControllerOutsideAppControllerThrows(): void
    {
        $this->routing->get('/demos', \stdClass::class, 'index');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid controller');
        $this->routing->dispatch('GET', '/demos');
    }
}
