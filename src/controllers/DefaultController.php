<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\HttpException;
use App\View\View;

final class DefaultController extends AppController
{
    public function __construct(private readonly View $view)
    {
    }

    public function error(HttpException $error): void
    {
        $statusCode = $error->statusCode();

        http_response_code($statusCode);
        $this->view->render('error', [
            'pageTitle' => 'error.' . $statusCode . '.title',
            'statusCode' => $statusCode,
            'leadKey' => 'error.' . $statusCode . '.lead',
        ]);
    }
}
