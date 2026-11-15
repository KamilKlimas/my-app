<?php

declare(strict_types=1);

namespace App\Http;

enum Access
{
    case Anyone;
    case Guest;
    case User;
    case Admin;
}
