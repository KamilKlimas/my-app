<?php

declare(strict_types=1);

namespace App\I18n;

final class Translator
{
    /** @param array<string, string> $messages */
    public function __construct(
        public readonly Locale $locale,
        private readonly array $messages,
    ) {
    }

    public static function forLocale(Locale $locale): self
    {
        return new self($locale, require self::cataloguePath($locale));
    }

    public static function cataloguePath(Locale $locale): string
    {
        return dirname(__DIR__, 2) . '/lang/' . $locale->value . '.php';
    }

    /** @param array<string, string|int> $parameters */
    public function translate(string $key, array $parameters = []): string
    {
        if (!isset($this->messages[$key])) {
            throw new MissingTranslation($key, $this->locale);
        }

        $replacements = [];

        foreach ($parameters as $name => $value) {
            $replacements['{' . $name . '}'] = (string) $value;
        }

        return strtr($this->messages[$key], $replacements);
    }
}
