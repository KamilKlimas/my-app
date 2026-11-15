<?php

declare(strict_types=1);

namespace App\Tests\Unit\I18n;

use App\I18n\Locale;
use App\I18n\MissingTranslation;
use App\I18n\Translator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase
{
    private const string MATCHES_PL = '{count, plural, one {# mecz} few {# mecze} many {# meczów} other {# meczu}}';
    private const string MATCHES_EN = '{count, plural, one {# match} other {# matches}}';

    public function testTranslateReturnsMessageForKey(): void
    {
        $translator = new Translator(Locale::En, ['login.title' => 'Log in']);

        $this->assertSame('Log in', $translator->translate('login.title'));
    }

    public function testTranslateReplacesNamedParameters(): void
    {
        $translator = new Translator(Locale::En, ['error.eyebrow' => 'Error {status} on {map}']);

        $this->assertSame('Error 404 on de_mirage', $translator->translate('error.eyebrow', ['status' => 404, 'map' => 'de_mirage']));
    }

    public function testParameterValueIsInsertedLiterallyNotParsedAsPattern(): void
    {
        $translator = new Translator(Locale::En, ['greeting' => 'Hi {name}']);

        $this->assertSame('Hi {other}', $translator->translate('greeting', ['name' => '{other}']));
    }

    public function testApostropheInTextStaysLiteral(): void
    {
        $translator = new Translator(Locale::En, ['login.no_account' => "Don't have an account?"]);

        $this->assertSame("Don't have an account?", $translator->translate('login.no_account'));
    }

    /** @return array<string, array{int, string}> */
    public static function polishPluralForms(): array
    {
        return [
            'one' => [1, '1 mecz'],
            'few for 2 to 4' => [3, '3 mecze'],
            'many for 5 to 21' => [5, '5 meczów'],
            'many for teens ending in 2 to 4' => [12, '12 meczów'],
            'few for 22' => [22, '22 mecze'],
            'many for zero' => [0, '0 meczów'],
        ];
    }

    #[DataProvider('polishPluralForms')]
    public function testPolishPluralPicksFormByCount(int $count, string $expected): void
    {
        $translator = new Translator(Locale::Pl, ['demos.match_count' => self::MATCHES_PL]);

        $this->assertSame($expected, $translator->translate('demos.match_count', ['count' => $count]));
    }

    public function testEnglishPluralUsesOneOnlyForOne(): void
    {
        $translator = new Translator(Locale::En, ['demos.match_count' => self::MATCHES_EN]);

        $this->assertSame('1 match', $translator->translate('demos.match_count', ['count' => 1]));
        $this->assertSame('2 matches', $translator->translate('demos.match_count', ['count' => 2]));
    }

    public function testDateSkeletonFollowsLocale(): void
    {
        $message = ['common.date_time' => '{at, date, ::MMMdHHmm}'];
        $playedAt = new \DateTimeImmutable('2026-10-06 21:40');

        $this->assertSame('6 paź, 21:40', (new Translator(Locale::Pl, $message))->translate('common.date_time', ['at' => $playedAt]));
        $this->assertSame('Oct 6, 21:40', (new Translator(Locale::En, $message))->translate('common.date_time', ['at' => $playedAt]));
    }

    public function testDecimalSeparatorFollowsLocale(): void
    {
        $message = ['demos.stats.lost_note' => '{average, number}'];

        $this->assertSame('11,9', (new Translator(Locale::Pl, $message))->translate('demos.stats.lost_note', ['average' => 11.9]));
        $this->assertSame('11.9', (new Translator(Locale::En, $message))->translate('demos.stats.lost_note', ['average' => 11.9]));
    }

    public function testMissingKeyThrows(): void
    {
        $translator = new Translator(Locale::Pl, []);

        $this->expectException(MissingTranslation::class);
        $translator->translate('login.title');
    }

    public function testInvalidPatternThrows(): void
    {
        $translator = new Translator(Locale::En, ['broken' => '{count, plural, one {# match}']);

        $this->expectException(\LogicException::class);
        $translator->translate('broken', ['count' => 1]);
    }

    public function testForLocaleLoadsCatalogueOfThatLocale(): void
    {
        $this->assertSame('Logowanie', Translator::forLocale(Locale::Pl)->translate('login.title'));
        $this->assertSame('Log in', Translator::forLocale(Locale::En)->translate('login.title'));
    }

    public function testEveryCatalogueHasTheSameKeys(): void
    {
        $polish = array_keys(require Translator::cataloguePath(Locale::Pl));
        $english = array_keys(require Translator::cataloguePath(Locale::En));

        $this->assertSame([], array_values(array_diff($polish, $english)), 'Keys only in pl.php');
        $this->assertSame([], array_values(array_diff($english, $polish)), 'Keys only in en.php');
    }

    public function testEveryCatalogueValueIsANonEmptyValidMessagePattern(): void
    {
        foreach (Locale::cases() as $locale) {
            foreach (require Translator::cataloguePath($locale) as $key => $message) {
                $label = $locale->value . ': ' . $key;
                $this->assertIsString($message, $label);
                $this->assertNotSame('', trim($message), $label);
                $this->assertNotNull(\MessageFormatter::create($locale->value, $message), $label . ' ' . intl_get_error_message());
            }
        }
    }
}
