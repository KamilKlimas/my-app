<?php

declare(strict_types=1);

namespace App\Controller;

final class DefaultController extends AppController
{
    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Page not found',
        ]);
    }
}
