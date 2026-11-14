<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fixture;

use App\Model\User;
use App\Repository\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<string, User> */
    private array $usersByEmail = [];

    public function add(User $user): void
    {
        $this->usersByEmail[strtolower($user->email)] = $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->usersByEmail[strtolower($email)] ?? null;
    }
}
