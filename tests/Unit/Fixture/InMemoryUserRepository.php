<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fixture;

use App\Model\Role;
use App\Model\User;
use App\Repository\DuplicateEmail;
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

    public function create(string $email, string $name, string $passwordHash, Role $role): void
    {
        if (isset($this->usersByEmail[strtolower($email)])) {
            throw new DuplicateEmail('E-mail already registered');
        }

        $this->add(new User(count($this->usersByEmail) + 1, $email, $name, $passwordHash, $role));
    }

    public function count(): int
    {
        return count($this->usersByEmail);
    }
}
