<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Model\Role;
use App\Model\User;
use App\Service\AuthService;
use App\Tests\Unit\Fixture\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class AuthServiceTest extends TestCase
{
    private User $user;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->user = new User(
            1,
            'player@example.com',
            'Player',
            password_hash('correct-password', PASSWORD_BCRYPT, ['cost' => 4]),
            Role::User,
        );
        $repository = new InMemoryUserRepository();
        $repository->add($this->user);
        $this->authService = new AuthService($repository);
    }

    public function testCorrectPasswordReturnsUser(): void
    {
        $this->assertSame($this->user, $this->authService->authenticate('player@example.com', 'correct-password'));
    }

    public function testWrongPasswordReturnsNull(): void
    {
        $this->assertNull($this->authService->authenticate('player@example.com', 'wrong-password'));
    }

    public function testUnknownEmailReturnsNull(): void
    {
        $this->assertNull($this->authService->authenticate('nobody@example.com', 'correct-password'));
    }

    public function testEmailLongerThan254CharactersReturnsNull(): void
    {
        $email = str_repeat('a', 243) . '@example.com';

        $this->assertNull($this->authService->authenticate($email, 'correct-password'));
    }

    public function testPasswordOfExactly72BytesReturnsUser(): void
    {
        $password = str_repeat('a', 72);
        $user = new User(2, 'long@example.com', 'Long', password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]), Role::User);
        $repository = new InMemoryUserRepository();
        $repository->add($user);

        $this->assertSame($user, (new AuthService($repository))->authenticate('long@example.com', $password));
    }

    public function testPasswordLongerThan72BytesReturnsNullEvenWithMatchingPrefix(): void
    {
        $password = str_repeat('a', 72);
        $user = new User(2, 'long@example.com', 'Long', password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]), Role::User);
        $repository = new InMemoryUserRepository();
        $repository->add($user);

        $this->assertNull((new AuthService($repository))->authenticate('long@example.com', $password . 'b'));
    }
}
