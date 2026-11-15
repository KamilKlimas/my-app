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

    /** @param array<string, string|int|float|\DateTimeInterface> $parameters */
    public function translate(string $key, array $parameters = []): string
    {
        if (!isset($this->messages[$key])) {
            throw new MissingTranslation($key, $this->locale);
        }

        $formatter = \MessageFormatter::create($this->locale->value, $this->messages[$key]);

        if ($formatter === null) {
            throw new \LogicException(sprintf('Invalid message "%s": %s', $key, intl_get_error_message()));
        }

        $text = $formatter->format($parameters);

        if ($text === false) {
            throw new \LogicException(sprintf('Cannot format "%s": %s', $key, $formatter->getErrorMessage()));
        }

        return $text;
    }
}
