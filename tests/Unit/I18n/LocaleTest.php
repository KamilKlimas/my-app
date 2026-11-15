<?php

declare(strict_types=1);

namespace App\Tests\Unit\I18n;

use App\I18n\Locale;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    public function testSessionChoiceWinsOverBrowserLanguage(): void
    {
        $this->assertSame(Locale::En, Locale::resolve('en', 'pl-PL,pl;q=0.9'));
    }

    public function testUnknownSessionValueFallsBackToBrowserLanguage(): void
    {
        $this->assertSame(Locale::Pl, Locale::resolve('de', 'pl-PL'));
    }

    /** @return array<string, array{?string, Locale}> */
    public static function browserLanguages(): array
    {
        return [
            'polish with region' => ['pl-PL,pl;q=0.9,en;q=0.8', Locale::Pl],
            'plain polish' => ['pl', Locale::Pl],
            'uppercase polish' => ['PL', Locale::Pl],
            'english' => ['en-US,en;q=0.9', Locale::En],
            'other language' => ['de-DE,de;q=0.9', Locale::En],
            'polish preferred by quality' => ['en;q=0.5, pl;q=0.9', Locale::Pl],
            'polish listed but less preferred' => ['de, pl;q=0.8', Locale::En],
            'polish refused with zero quality' => ['pl;q=0', Locale::En],
            'wildcard' => ['*', Locale::En],
            'empty header' => ['', Locale::En],
            'missing header' => [null, Locale::En],
            'garbage' => [';;,,q=abc', Locale::En],
        ];
    }

    #[DataProvider('browserLanguages')]
    public function testWithoutSessionChoiceBrowserLanguagePicksPolishOrEnglish(?string $acceptLanguage, Locale $expected): void
    {
        $this->assertSame($expected, Locale::resolve(null, $acceptLanguage));
    }
}
