<?php

declare(strict_types=1);

namespace App\I18n;

final class MissingTranslation extends \LogicException
{
    public function __construct(string $key, Locale $locale)
    {
        parent::__construct(sprintf('Missing translation "%s" for locale "%s"', $key, $locale->value));
    }
}
