<?php

declare(strict_types=1);

namespace App\Controller;

final class DefaultController extends AppController
{
    // 302, not 301: browsers cache a 301 for good, and after login "/" may point somewhere else.
    public function index(): void
    {
        header('Location: /demos', true, 302);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Nie znaleziono strony',
        ]);
    }
}
