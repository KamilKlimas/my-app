<?php

declare(strict_types=1);

namespace App\Model;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
        public string $passwordHash,
        public Role $role,
    ) {
    }
}
