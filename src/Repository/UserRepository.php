<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

interface UserRepository
{
    public function findByEmail(string $email): ?User;
}
