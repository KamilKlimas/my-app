<?php

declare(strict_types=1);

namespace App\Repository;

use App\I18n\Locale;
use App\Model\Role;
use App\Model\User;

final class PdoUserRepository implements UserRepository
{
    private const string UNIQUE_VIOLATION = '23505';

    public function __construct(private readonly \PDO $database)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->database->prepare(
            'SELECT id, email, name, password_hash, role, locale FROM users WHERE LOWER(email) = LOWER(:email)',
        );
        $statement->execute(['email' => $email]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapRow($row);
    }

    public function findById(int $id): ?User
    {
        $statement = $this->database->prepare(
            'SELECT id, email, name, password_hash, role, locale FROM users WHERE id = :id',
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapRow($row);
    }

    public function create(string $email, string $name, string $passwordHash, Role $role, Locale $locale): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO users (email, name, password_hash, role, locale)'
            . ' VALUES (:email, :name, :password_hash, :role, :locale)',
        );

        try {
            $statement->execute([
                'email' => $email,
                'name' => $name,
                'password_hash' => $passwordHash,
                'role' => $role->value,
                'locale' => $locale->value,
            ]);
        } catch (\PDOException $exception) {
            if (($exception->errorInfo[0] ?? null) === self::UNIQUE_VIOLATION) {
                throw new DuplicateEmail('E-mail already registered', 0, $exception);
            }

            throw $exception;
        }
    }

    public function updateLocale(int $id, Locale $locale): void
    {
        $statement = $this->database->prepare('UPDATE users SET locale = :locale WHERE id = :id');
        $statement->execute(['locale' => $locale->value, 'id' => $id]);
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
            Locale::from((string) $row['locale']),
        );
    }
}
