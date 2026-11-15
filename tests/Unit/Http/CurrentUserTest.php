<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\CurrentUser;
use App\Http\Session;
use App\Model\Role;
use App\Model\User;
use App\Tests\Unit\Fixture\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class CurrentUserTest extends TestCase
{
    private User $user;
    private InMemoryUserRepository $repository;

    protected function setUp(): void
    {
        $this->user = new User(5, 'player@example.com', 'Player', 'hash', Role::User);
        $this->repository = new InMemoryUserRepository();
        $this->repository->add($this->user);
    }

    public function testUserIsNullWithoutIdInSession(): void
    {
        $data = [];

        $this->assertNull((new CurrentUser(new Session($data), $this->repository))->user());
    }

    public function testUserIsLoadedFromIdInSession(): void
    {
        $data = [CurrentUser::SESSION_KEY => 5];

        $this->assertSame($this->user, (new CurrentUser(new Session($data), $this->repository))->user());
    }

    public function testUnknownIdReturnsNullAndIsRemovedFromSession(): void
    {
        $data = [CurrentUser::SESSION_KEY => 99, 'csrf_token' => 'abc'];

        $this->assertNull((new CurrentUser(new Session($data), $this->repository))->user());
        $this->assertSame(['csrf_token' => 'abc'], $data);
    }

    public function testUserIsLoadedFromRepositoryOnlyOnce(): void
    {
        $data = [CurrentUser::SESSION_KEY => 5];
        $currentUser = new CurrentUser(new Session($data), $this->repository);

        $currentUser->user();
        $currentUser->user();

        $this->assertSame(1, $this->repository->findByIdCalls);
    }
}
