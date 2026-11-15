<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\Access;
use App\Http\AccessGuard;
use App\Http\CurrentUser;
use App\Http\Forbidden;
use App\Http\Redirect;
use App\Http\Session;
use App\I18n\Locale;
use App\Model\Role;
use App\Model\User;
use App\Tests\Unit\Fixture\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class AccessGuardTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $sessionData = [];

    public function testAnyoneRoutePassesForGuest(): void
    {
        $this->expectNotToPerformAssertions();
        $this->guardFor(null)->check(Access::Anyone);
    }

    public function testGuestRoutePassesForGuest(): void
    {
        $this->expectNotToPerformAssertions();
        $this->guardFor(null)->check(Access::Guest);
    }

    public function testGuestRouteRedirectsLoggedInUserToDemos(): void
    {
        $this->assertRedirectsTo('/demos', $this->guardFor(Role::User), Access::Guest);
    }

    public function testUserRouteRedirectsGuestToLogin(): void
    {
        $this->assertRedirectsTo('/login', $this->guardFor(null), Access::User);
    }

    public function testUserRoutePassesForUser(): void
    {
        $this->expectNotToPerformAssertions();
        $this->guardFor(Role::User)->check(Access::User);
    }

    public function testUserRoutePassesForAdmin(): void
    {
        $this->expectNotToPerformAssertions();
        $this->guardFor(Role::Admin)->check(Access::User);
    }

    public function testAdminRouteRedirectsGuestToLogin(): void
    {
        $this->assertRedirectsTo('/login', $this->guardFor(null), Access::Admin);
    }

    public function testAdminRouteForbidsUser(): void
    {
        $this->expectException(Forbidden::class);
        $this->guardFor(Role::User)->check(Access::Admin);
    }

    public function testAdminRoutePassesForAdmin(): void
    {
        $this->expectNotToPerformAssertions();
        $this->guardFor(Role::Admin)->check(Access::Admin);
    }

    private function guardFor(?Role $role): AccessGuard
    {
        $repository = new InMemoryUserRepository();

        if ($role !== null) {
            $repository->add(new User(5, 'player@example.com', 'Player', 'hash', $role, Locale::Pl));
            $this->sessionData = [CurrentUser::SESSION_KEY => 5];
        }

        return new AccessGuard(new CurrentUser(new Session($this->sessionData), $repository));
    }

    private function assertRedirectsTo(string $path, AccessGuard $guard, Access $access): void
    {
        try {
            $guard->check($access);
        } catch (Redirect $redirect) {
            $this->assertSame($path, $redirect->path());

            return;
        }

        $this->fail('Expected a redirect to ' . $path);
    }
}
