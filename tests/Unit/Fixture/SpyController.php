<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fixture;

use App\Controller\AppController;

final class SpyController extends AppController
{
    public function index(): void
    {
        echo 'index';
    }

    public function other(): void
    {
        echo 'other';
    }

    public function show(mixed $id): void
    {
        echo 'show:' . get_debug_type($id) . ':' . $id;
    }
}
