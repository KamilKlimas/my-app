<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Role;
use App\Model\User;

final class PdoUserRepository implements UserRepository
{
    public function __construct(private readonly \PDO $database)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->database->prepare(
            'SELECT id, email, name, password_hash, role FROM users WHERE LOWER(email) = LOWER(:email)',
        );
        $statement->execute(['email' => $email]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapRow($row);
    }

    /** @param array<string, mixed> $row */
    private function mapRow(array $row): User
    {
        return new User(
            (int) $row['id'],
            (string) $row['email'],
            (string) $row['name'],
            (string) $row['password_hash'],
            Role::from((string) $row['role']),
        );
    }
}
