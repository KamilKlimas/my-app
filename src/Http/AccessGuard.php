<?php

declare(strict_types=1);

namespace App\Http;

use App\Model\Role;

final class AccessGuard
{
    public function __construct(private readonly CurrentUser $currentUser)
    {
    }

    public function check(Access $access): void
    {
        if ($access === Access::Anyone) {
            return;
        }

        $user = $this->currentUser->user();

        if ($access === Access::Guest) {
            if ($user !== null) {
                throw new Redirect('/demos');
            }

            return;
        }

        if ($user === null) {
            throw new Redirect('/login');
        }

        if ($access === Access::Admin && $user->role !== Role::Admin) {
            throw new Forbidden();
        }
    }
}
