<?php

declare(strict_types=1);

namespace App\Database;

final class ConnectionFailed extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Database connection failed');
    }
}
