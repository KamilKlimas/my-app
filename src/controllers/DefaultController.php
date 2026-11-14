<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\HttpException;

final class DefaultController extends AppController
{
    private const PAGES = [
        400 => [
            'title' => 'Bad request',
            'lead' => 'The address has a malformed parameter. Check the link or start again from your demos.',
        ],
        403 => [
            'title' => 'Access denied',
            'lead' => "You don't have permission to do this.",
        ],
        404 => [
            'title' => 'Page not found',
            'lead' => "This page doesn't exist or the record was removed.",
        ],
        503 => [
            'title' => 'Service unavailable',
            'lead' => 'DuelLab cannot reach its database right now. Try again in a moment.',
        ],
    ];

    public function error(HttpException $error): void
    {
        $statusCode = $error->statusCode();
        $page = self::PAGES[$statusCode];

        http_response_code($statusCode);
        $this->render('error', [
            'pageTitle' => $page['title'],
            'statusCode' => $statusCode,
            'lead' => $page['lead'],
        ]);
    }
}
