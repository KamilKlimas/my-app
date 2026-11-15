<?php

declare(strict_types=1);

namespace App\Service;

use App\I18n\Locale;
use App\Model\Role;
use App\Model\User;
use App\Repository\DuplicateEmail;
use App\Repository\UserRepository;

final class AuthService
{
    private const string DUMMY_HASH = '$2y$10$sh6gcqSE/Yev6w1eaFLRFOUOIJt.iQFEsP0Ve2T5sG9G/DzdUX.vi';
    private const int MAX_EMAIL_LENGTH = 254;
    private const int MAX_PASSWORD_BYTES = 72;
    private const int MIN_PASSWORD_LENGTH = 8;
    private const string NAME_PATTERN = '/^[\p{L}\p{N} _.-]{3,32}\z/u';

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function authenticate(string $email, #[\SensitiveParameter] string $password): ?User
    {
        if (mb_strlen($email) > self::MAX_EMAIL_LENGTH || strlen($password) > self::MAX_PASSWORD_BYTES) {
            return null;
        }

        $user = $this->users->findByEmail($email);

        if ($user === null) {
            password_verify($password, self::DUMMY_HASH);

            return null;
        }

        return password_verify($password, $user->passwordHash) ? $user : null;
    }

    /** @return array<string, string> field => error code; empty when the form was accepted */
    public function register(
        string $email,
        string $name,
        #[\SensitiveParameter] string $password,
        #[\SensitiveParameter] string $passwordConfirmation,
        Locale $locale,
    ): array {
        $errors = $this->validateRegistration($email, $name, $password, $passwordConfirmation);

        if ($errors !== []) {
            return $errors;
        }

        try {
            $this->users->create($email, $name, password_hash($password, PASSWORD_DEFAULT), Role::User, $locale);
        } catch (DuplicateEmail) {
            // Same result as success: the form must not reveal which e-mails have accounts.
        }

        return [];
    }

    /** @return array<string, string> */
    private function validateRegistration(string $email, string $name, string $password, string $confirmation): array
    {
        $errors = [];

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($email) > self::MAX_EMAIL_LENGTH) {
            $errors['email'] = 'email_invalid';
        }
        if (preg_match(self::NAME_PATTERN, $name) !== 1) {
            $errors['name'] = 'name_invalid';
        }
        if (mb_strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $errors['password'] = 'password_too_short';
        } elseif (strlen($password) > self::MAX_PASSWORD_BYTES) {
            $errors['password'] = 'password_too_long';
        } elseif ($password !== $confirmation) {
            $errors['password_confirmation'] = 'password_mismatch';
        }

        return $errors;
    }
}
