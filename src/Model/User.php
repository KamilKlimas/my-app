<?php

declare(strict_types=1);

namespace App\Model;

use App\I18n\Locale;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
        public string $passwordHash,
        public Role $role,
        public Locale $locale,
    ) {
    }
}
