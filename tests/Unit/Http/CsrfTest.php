<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\Csrf;
use App\Http\Forbidden;
use App\Http\Session;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    public function testEnsureTokenStoresSixtyFourHexCharactersInSession(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));

        $csrf->ensureToken();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $data['csrf_token']);
    }

    public function testEnsureTokenKeepsExistingToken(): void
    {
        $data = ['csrf_token' => 'existing'];
        $csrf = new Csrf(new Session($data));

        $csrf->ensureToken();

        $this->assertSame('existing', $data['csrf_token']);
    }

    public function testTokensDifferBetweenSessions(): void
    {
        $firstData = [];
        $secondData = [];

        (new Csrf(new Session($firstData)))->ensureToken();
        (new Csrf(new Session($secondData)))->ensureToken();

        $this->assertNotSame($firstData['csrf_token'], $secondData['csrf_token']);
    }

    public function testRotateReplacesExistingToken(): void
    {
        $data = ['csrf_token' => 'existing'];
        $csrf = new Csrf(new Session($data));

        $csrf->rotate();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $data['csrf_token']);
    }

    public function testOldTokenIsForbiddenAfterRotate(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));
        $csrf->ensureToken();
        $oldToken = $csrf->token();

        $csrf->rotate();

        $this->expectException(Forbidden::class);
        $csrf->verifyRequest('POST', [Csrf::FIELD_NAME => $oldToken]);
    }

    public function testTokenReturnsStoredToken(): void
    {
        $data = ['csrf_token' => 'stored'];
        $csrf = new Csrf(new Session($data));

        $this->assertSame('stored', $csrf->token());
    }

    public function testTokenBeforeEnsureTokenThrowsAndWritesNothing(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));

        try {
            $csrf->token();
            $this->fail('Expected LogicException');
        } catch (\LogicException) {
            $this->assertSame([], $data);
        }
    }

    public function testPostWithMatchingTokenPasses(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));
        $csrf->ensureToken();

        $csrf->verifyRequest('POST', [Csrf::FIELD_NAME => $csrf->token()]);

        $this->addToAssertionCount(1);
    }

    public function testGetWithoutTokenPasses(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));

        $csrf->verifyRequest('GET', []);

        $this->addToAssertionCount(1);
    }

    public function testPostWithoutTokenFieldIsForbidden(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));
        $csrf->ensureToken();

        $this->expectException(Forbidden::class);
        $csrf->verifyRequest('POST', []);
    }

    public function testPostWithWrongTokenIsForbidden(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));
        $csrf->ensureToken();

        $this->expectException(Forbidden::class);
        $csrf->verifyRequest('POST', [Csrf::FIELD_NAME => str_repeat('0', 64)]);
    }

    public function testPostWithEmptyTokenIsForbidden(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));
        $csrf->ensureToken();

        $this->expectException(Forbidden::class);
        $csrf->verifyRequest('POST', [Csrf::FIELD_NAME => '']);
    }

    public function testPostWithArrayTokenIsForbidden(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));
        $csrf->ensureToken();

        $this->expectException(Forbidden::class);
        $csrf->verifyRequest('POST', [Csrf::FIELD_NAME => [$csrf->token()]]);
    }

    public function testPostIsForbiddenWhenSessionHasNoToken(): void
    {
        $data = [];
        $csrf = new Csrf(new Session($data));

        $this->expectException(Forbidden::class);
        $csrf->verifyRequest('POST', [Csrf::FIELD_NAME => '']);
    }
}
