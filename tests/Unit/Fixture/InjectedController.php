<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fixture;

use App\Controller\AppController;

final class InjectedController extends AppController
{
    public function __construct(private readonly string $marker)
    {
    }

    public function index(): void
    {
        echo $this->marker;
    }
}
