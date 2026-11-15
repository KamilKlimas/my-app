<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http;

use App\Http\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testStringReturnsStoredValue(): void
    {
        $data = ['csrf' => 'abc'];
        $session = new Session($data);

        $this->assertSame('abc', $session->string('csrf'));
    }

    public function testStringReturnsNullWhenKeyIsMissing(): void
    {
        $data = [];
        $session = new Session($data);

        $this->assertNull($session->string('csrf'));
    }

    public function testStringRejectsValueOfAnotherType(): void
    {
        $data = ['user_id' => 7];
        $session = new Session($data);

        $this->expectException(\UnexpectedValueException::class);
        $session->string('user_id');
    }

    public function testIntReturnsStoredValue(): void
    {
        $data = ['user_id' => 7];
        $session = new Session($data);

        $this->assertSame(7, $session->int('user_id'));
    }

    public function testIntReturnsNullWhenKeyIsMissing(): void
    {
        $data = [];
        $session = new Session($data);

        $this->assertNull($session->int('user_id'));
    }

    public function testIntRejectsNumericString(): void
    {
        $data = ['user_id' => '7'];
        $session = new Session($data);

        $this->expectException(\UnexpectedValueException::class);
        $session->int('user_id');
    }

    public function testSetWritesIntoTheWrappedArray(): void
    {
        $data = [];
        $session = new Session($data);

        $session->set('csrf', 'abc');

        $this->assertSame(['csrf' => 'abc'], $data);
    }

    public function testRemoveDeletesOnlyThatKey(): void
    {
        $data = ['user_id' => 7, 'csrf' => 'abc'];
        (new Session($data))->remove('user_id');

        $this->assertSame(['csrf' => 'abc'], $data);
    }

    public function testRemoveOfMissingKeyLeavesDataUntouched(): void
    {
        $data = ['csrf' => 'abc'];
        (new Session($data))->remove('user_id');

        $this->assertSame(['csrf' => 'abc'], $data);
    }

    public function testSecureFromEnvironmentAcceptsOne(): void
    {
        $this->assertTrue(Session::secureFromEnvironment(['SESSION_SECURE' => '1']));
    }

    public function testSecureFromEnvironmentAcceptsZero(): void
    {
        $this->assertFalse(Session::secureFromEnvironment(['SESSION_SECURE' => '0']));
    }

    public function testSecureFromEnvironmentRejectsOtherValue(): void
    {
        $this->expectException(\RuntimeException::class);
        Session::secureFromEnvironment(['SESSION_SECURE' => 'true']);
    }

    public function testSecureFromEnvironmentRejectsMissingVariable(): void
    {
        $this->expectException(\RuntimeException::class);
        Session::secureFromEnvironment([]);
    }
}
