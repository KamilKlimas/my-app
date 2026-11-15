<?php

declare(strict_types=1);

namespace App\Service;

use App\I18n\Locale;
use App\Repository\UserRepository;

final class AccountService
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function changeLocale(int $userId, Locale $locale): void
    {
        $this->users->updateLocale($userId, $locale);
    }
}
