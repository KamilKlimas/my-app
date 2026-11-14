<?php

declare(strict_types=1);

namespace App\Model;

enum Role: string
{
    case User = 'USER';
    case Admin = 'ADMIN';
}
