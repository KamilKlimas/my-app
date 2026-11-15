<?php

declare(strict_types=1);

namespace App\View;

use App\Http\Csrf;
use App\I18n\Translator;

final class View
{
    public function __construct(
        private readonly Translator $translator,
        private readonly Csrf $csrf,
        private readonly string $currentPath,
    ) {
    }

    public function render(string $template, array $variables = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/public/views/' . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        $variables += [
            'e' => static fn (mixed $value): string => htmlspecialchars((string) $value),
            't' => fn (string $key, array $parameters = []): string => $this->translator->translate($key, $parameters),
            'lang' => $this->translator->locale->value,
            'csrfToken' => $this->csrf->token(),
            'currentPath' => $this->currentPath,
        ];

        extract($variables, EXTR_SKIP);
        require $templatePath;
    }
}
