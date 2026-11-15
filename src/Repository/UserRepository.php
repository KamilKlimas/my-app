<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Role;
use App\Model\User;

interface UserRepository
{
    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    /** @throws DuplicateEmail */
    public function create(string $email, string $name, string $passwordHash, Role $role): void;
}
