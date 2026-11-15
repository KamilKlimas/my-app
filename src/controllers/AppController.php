<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Csrf;
use App\Http\Session;

abstract class AppController
{
    protected function render(string $template, array $variables = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/public/views/' . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        $variables += [
            'e' => static fn (mixed $value): string => htmlspecialchars((string) $value),
            'csrfToken' => (new Csrf(new Session($_SESSION)))->token(),
        ];

        extract($variables, EXTR_SKIP);
        require $templatePath;
    }
}