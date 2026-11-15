<?php

declare(strict_types=1);

namespace App\I18n;

enum Locale: string
{
    case Pl = 'pl';
    case En = 'en';

    public const string SESSION_KEY = 'locale';

    public static function resolve(?string $sessionValue, ?string $acceptLanguage): self
    {
        $chosen = self::tryFrom($sessionValue ?? '');

        if ($chosen !== null) {
            return $chosen;
        }

        return self::preferredLanguage($acceptLanguage ?? '') === 'pl' ? self::Pl : self::En;
    }

    private static function preferredLanguage(string $acceptLanguage): string
    {
        $bestLanguage = '';
        $bestQuality = 0.0;

        foreach (explode(',', $acceptLanguage) as $range) {
            $parts = explode(';', $range);
            $language = strtolower(explode('-', trim($parts[0]))[0]);
            $quality = 1.0;

            foreach (array_slice($parts, 1) as $parameter) {
                if (preg_match('/^\s*q\s*=\s*([01](?:\.\d{0,3})?)\s*$/i', $parameter, $match)) {
                    $quality = (float) $match[1];
                }
            }

            if ($language !== '' && $quality > $bestQuality) {
                $bestLanguage = $language;
                $bestQuality = $quality;
            }
        }

        return $bestLanguage;
    }
}
