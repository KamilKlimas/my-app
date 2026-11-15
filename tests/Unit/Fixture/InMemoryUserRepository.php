<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fixture;

use App\I18n\Locale;
use App\Model\Role;
use App\Model\User;
use App\Repository\DuplicateEmail;
use App\Repository\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<string, User> */
    private array $usersByEmail = [];

    public int $findByIdCalls = 0;

    public function add(User $user): void
    {
        $this->usersByEmail[strtolower($user->email)] = $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->usersByEmail[strtolower($email)] ?? null;
    }

    public function findById(int $id): ?User
    {
        $this->findByIdCalls++;

        foreach ($this->usersByEmail as $user) {
            if ($user->id === $id) {
                return $user;
            }
        }

        return null;
    }

    public function create(string $email, string $name, string $passwordHash, Role $role, Locale $locale): void
    {
        if (isset($this->usersByEmail[strtolower($email)])) {
            throw new DuplicateEmail('E-mail already registered');
        }

        $this->add(new User(count($this->usersByEmail) + 1, $email, $name, $passwordHash, $role, $locale));
    }

    public function updateLocale(int $id, Locale $locale): void
    {
        foreach ($this->usersByEmail as $key => $user) {
            if ($user->id === $id) {
                $this->usersByEmail[$key] = new User($user->id, $user->email, $user->name, $user->passwordHash, $user->role, $locale);
            }
        }
    }

    public function count(): int
    {
        return count($this->usersByEmail);
    }
}
