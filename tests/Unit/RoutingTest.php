<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Http\Access;
use App\Http\AccessGuard;
use App\Http\CurrentUser;
use App\Http\Redirect;
use App\Http\Session;
use App\Model\Role;
use App\Model\User;
use App\Routing;
use App\Tests\Unit\Fixture\InMemoryUserRepository;
use App\Tests\Unit\Fixture\InjectedController;
use App\Tests\Unit\Fixture\SpyController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RoutingTest extends TestCase
{
    private Routing $routing;

    /** @var array<string, mixed> */
    private array $sessionData = [];

    private InMemoryUserRepository $users;

    protected function setUp(): void
    {
        $this->users = new InMemoryUserRepository();
        $this->routing = new Routing(new AccessGuard(new CurrentUser(new Session($this->sessionData), $this->users)));
    }

    public function testStaticRouteCallsItsAction(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index', Access::Anyone);

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('GET', '/demos'));
    }

    public function testStaticRouteDoesNotMatchAnotherPath(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index', Access::Anyone);

        $this->expectOutputString('');
        $this->assertFalse($this->routing->dispatch('GET', '/hotspots'));
    }

    public function testGetRouteDoesNotMatchPostRequest(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index', Access::Anyone);

        $this->expectOutputString('');
        $this->assertFalse($this->routing->dispatch('POST', '/demos'));
    }

    public function testPostRouteMatchesPostRequest(): void
    {
        $this->routing->post('/demos', SpyController::class, 'index', Access::Anyone);

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('POST', '/demos'));
    }

    public function testNumericParameterIsPassedAsNamedInt(): void
    {
        $this->routing->get('/demos/{id}', SpyController::class, 'show', Access::Anyone);

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
        $this->routing->get('/demos/{id}', SpyController::class, 'show', Access::Anyone);

        $this->expectOutputString('');
        $this->assertFalse($this->routing->dispatch('GET', $path));
    }

    public function testQueryStringIsIgnoredWhenMatching(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index', Access::Anyone);

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('GET', '/demos?map=de_mirage'));
    }

    public function testFirstMatchingRouteWins(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index', Access::Anyone);
        $this->routing->get('/demos', SpyController::class, 'other', Access::Anyone);

        $this->expectOutputString('index');
        $this->routing->dispatch('GET', '/demos');
    }

    public function testControllerOutsideAppControllerThrows(): void
    {
        $this->routing->get('/demos', \stdClass::class, 'index', Access::Anyone);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid controller');
        $this->routing->dispatch('GET', '/demos');
    }

    public function testFactoryRouteCallsActionOnControllerFromFactory(): void
    {
        $this->routing->get('/demos', static fn (): InjectedController => new InjectedController('injected'), 'index', Access::Anyone);

        $this->expectOutputString('injected');
        $this->assertTrue($this->routing->dispatch('GET', '/demos'));
    }

    public function testFactoryIsNotCalledWhenRouteDoesNotMatch(): void
    {
        $factoryCalled = false;
        $this->routing->get('/demos', static function () use (&$factoryCalled): InjectedController {
            $factoryCalled = true;

            return new InjectedController('injected');
        }, 'index', Access::Anyone);

        $this->routing->dispatch('GET', '/hotspots');

        $this->assertFalse($factoryCalled);
    }

    public function testFactoryReturningNonAppControllerThrows(): void
    {
        $this->routing->get('/demos', static fn (): \stdClass => new \stdClass(), 'index', Access::Anyone);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid controller');
        $this->routing->dispatch('GET', '/demos');
    }

    public function testGuestOnUserRouteIsRedirectedToLogin(): void
    {
        $this->routing->get('/demos', SpyController::class, 'index', Access::User);

        $this->expectException(Redirect::class);
        $this->routing->dispatch('GET', '/demos');
    }

    public function testControllerIsNotCreatedWhenGuardRejectsRoute(): void
    {
        $factoryCalled = false;
        $this->routing->get('/demos', static function () use (&$factoryCalled): InjectedController {
            $factoryCalled = true;

            return new InjectedController('injected');
        }, 'index', Access::User);

        try {
            $this->routing->dispatch('GET', '/demos');
        } catch (Redirect) {
        }

        $this->assertFalse($factoryCalled);
    }

    public function testLoggedInUserReachesUserRoute(): void
    {
        $this->users->add(new User(5, 'player@example.com', 'Player', 'hash', Role::User));
        $this->sessionData[CurrentUser::SESSION_KEY] = 5;
        $this->routing->get('/demos', SpyController::class, 'index', Access::User);

        $this->expectOutputString('index');
        $this->assertTrue($this->routing->dispatch('GET', '/demos'));
    }
}
