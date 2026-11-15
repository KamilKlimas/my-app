<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepository;

final class AuthService
{
    private const string DUMMY_HASH = '$2y$10$sh6gcqSE/Yev6w1eaFLRFOUOIJt.iQFEsP0Ve2T5sG9G/DzdUX.vi';
    private const int MAX_EMAIL_LENGTH = 254;
    private const int MAX_PASSWORD_BYTES = 72;

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
}
