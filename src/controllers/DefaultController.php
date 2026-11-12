<?php

declare(strict_types=1);

namespace App\Controller;

final class DefaultController extends AppController
{
    public function notFound(): void
    {
        $this->render('error', [
            'pageTitle' => 'Page not found',
            'statusCode' => 404,
            'lead' => "This page doesn't exist or the record was removed.",
        ]);
    }

    public function badRequest(): void
    {
        $this->render('error', [
            'pageTitle' => 'Bad request',
            'statusCode' => 400,
            'lead' => 'The address has a malformed parameter. Check the link or start again from your demos.',
        ]);
    }
}
