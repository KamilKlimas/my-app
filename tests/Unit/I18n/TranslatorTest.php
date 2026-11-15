<?php

declare(strict_types=1);

namespace App\Tests\Unit\I18n;

use App\I18n\Locale;
use App\I18n\MissingTranslation;
use App\I18n\Translator;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase
{
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

    public function testTranslateLeavesUnknownPlaceholderAndDoesNotReplaceInsideParameterValue(): void
    {
        $translator = new Translator(Locale::En, ['greeting' => 'Hi {name}, {other}']);

        $this->assertSame('Hi {other}, {other}', $translator->translate('greeting', ['name' => '{other}']));
    }

    public function testMissingKeyThrows(): void
    {
        $translator = new Translator(Locale::Pl, []);

        $this->expectException(MissingTranslation::class);
        $translator->translate('login.title');
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

    public function testEveryCatalogueValueIsANonEmptyString(): void
    {
        foreach (Locale::cases() as $locale) {
            foreach (require Translator::cataloguePath($locale) as $key => $message) {
                $this->assertIsString($message, $locale->value . ': ' . $key);
                $this->assertNotSame('', trim($message), $locale->value . ': ' . $key);
            }
        }
    }
}
