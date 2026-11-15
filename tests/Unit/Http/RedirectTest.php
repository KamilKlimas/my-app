<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\Redirect;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RedirectTest extends TestCase
{
    public static function localPaths(): array
    {
        return [
            'root' => ['/'],
            'page' => ['/demos'],
            'page with query' => ['/demos?map=de_mirage'],
        ];
    }

    public static function foreignTargets(): array
    {
        return [
            'absolute url' => ['https://evil.example/login'],
            'protocol relative url' => ['//evil.example/login'],
            'backslash after slash' => ['/\\evil.example/login'],
            'relative path without slash' => ['demos'],
            'empty' => [''],
        ];
    }

    #[DataProvider('localPaths')]
    public function testRedirectKeepsLocalPath(string $path): void
    {
        $this->assertSame($path, (new Redirect($path))->path());
    }

    #[DataProvider('localPaths')]
    public function testLocalPathIsRecognisedAsLocal(string $path): void
    {
        $this->assertTrue(Redirect::isLocalPath($path));
    }

    #[DataProvider('foreignTargets')]
    public function testForeignTargetIsNotRecognisedAsLocal(string $target): void
    {
        $this->assertFalse(Redirect::isLocalPath($target));
    }

    #[DataProvider('foreignTargets')]
    public function testRedirectRejectsTargetOutsideTheApp(string $target): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Redirect($target);
    }
}
