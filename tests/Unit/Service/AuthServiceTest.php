<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\I18n\Locale;
use App\Model\Role;
use App\Model\User;
use App\Service\AuthService;
use App\Tests\Unit\Fixture\InMemoryUserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AuthServiceTest extends TestCase
{
    private User $user;
    private InMemoryUserRepository $repository;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->user = new User(
            1,
            'player@example.com',
            'Player',
            password_hash('correct-password', PASSWORD_BCRYPT, ['cost' => 4]),
            Role::User,
            Locale::Pl,
        );
        $this->repository = new InMemoryUserRepository();
        $this->repository->add($this->user);
        $this->authService = new AuthService($this->repository);
    }

    public function testCorrectPasswordReturnsUser(): void
    {
        $this->assertSame($this->user, $this->authService->authenticate('player@example.com', 'correct-password'));
    }

    public function testWrongPasswordReturnsNull(): void
    {
        $this->assertNull($this->authService->authenticate('player@example.com', 'wrong-password'));
    }

    public function testUnknownEmailReturnsNull(): void
    {
        $this->assertNull($this->authService->authenticate('nobody@example.com', 'correct-password'));
    }

    public function testEmailLongerThan254CharactersReturnsNull(): void
    {
        $email = str_repeat('a', 243) . '@example.com';

        $this->assertNull($this->authService->authenticate($email, 'correct-password'));
    }

    public function testPasswordOfExactly72BytesReturnsUser(): void
    {
        $password = str_repeat('a', 72);
        $user = new User(2, 'long@example.com', 'Long', password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]), Role::User, Locale::Pl);
        $repository = new InMemoryUserRepository();
        $repository->add($user);

        $this->assertSame($user, (new AuthService($repository))->authenticate('long@example.com', $password));
    }

    public function testPasswordLongerThan72BytesReturnsNullEvenWithMatchingPrefix(): void
    {
        $password = str_repeat('a', 72);
        $user = new User(2, 'long@example.com', 'Long', password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]), Role::User, Locale::Pl);
        $repository = new InMemoryUserRepository();
        $repository->add($user);

        $this->assertNull((new AuthService($repository))->authenticate('long@example.com', $password . 'b'));
    }

    public function testValidRegistrationReturnsNoErrors(): void
    {
        $this->assertSame([], $this->authService->register('new@example.com', 'NewPlayer', 'secret-pass', 'secret-pass', Locale::En));
    }

    public function testValidRegistrationStoresUserRoleAndVerifiableHash(): void
    {
        $this->authService->register('new@example.com', 'NewPlayer', 'secret-pass', 'secret-pass', Locale::En);
        $stored = $this->repository->findByEmail('new@example.com');

        $this->assertSame(Role::User, $stored?->role);
        $this->assertTrue(password_verify('secret-pass', $stored->passwordHash));
    }

    /** @return array<string, array{Locale}> */
    public static function locales(): array
    {
        return ['polish' => [Locale::Pl], 'english' => [Locale::En]];
    }

    #[DataProvider('locales')]
    public function testRegistrationStoresLocaleOfTheForm(Locale $locale): void
    {
        $this->authService->register('new@example.com', 'NewPlayer', 'secret-pass', 'secret-pass', $locale);

        $this->assertSame($locale, $this->repository->findByEmail('new@example.com')?->locale);
    }

    public function testRegisteredAccountCanAuthenticate(): void
    {
        $this->authService->register('new@example.com', 'NewPlayer', 'secret-pass', 'secret-pass', Locale::En);

        $this->assertSame('NewPlayer', $this->authService->authenticate('new@example.com', 'secret-pass')?->name);
    }

    /** @return array<string, array{string, string, string, string}> */
    public static function acceptedBoundaryRegistrations(): array
    {
        return [
            'password of exactly 8 characters' => ['a@example.com', 'Player', '12345678', '12345678'],
            'password of exactly 72 bytes' => ['a@example.com', 'Player', str_repeat('a', 72), str_repeat('a', 72)],
            'name of exactly 3 characters' => ['a@example.com', 'Ola', 'secret-pass', 'secret-pass'],
            'name of exactly 32 characters' => ['a@example.com', str_repeat('a', 32), 'secret-pass', 'secret-pass'],
            'name with polish letters, space, dot and dash' => ['a@example.com', 'Łukasz J.-K_1', 'secret-pass', 'secret-pass'],
        ];
    }

    #[DataProvider('acceptedBoundaryRegistrations')]
    public function testBoundaryRegistrationIsAccepted(string $email, string $name, string $password, string $confirmation): void
    {
        $this->assertSame([], $this->authService->register($email, $name, $password, $confirmation, Locale::En));
    }

    /** @return array<string, array{string, string, string, string, array<string, string>}> */
    public static function rejectedRegistrations(): array
    {
        $longEmail = str_repeat('a', 243) . '@example.com';
        $longPassword = str_repeat('a', 73);

        return [
            'malformed e-mail' => ['not-an-email', 'Player', 'secret-pass', 'secret-pass', ['email' => 'email_invalid']],
            'e-mail longer than 254 characters' => [$longEmail, 'Player', 'secret-pass', 'secret-pass', ['email' => 'email_invalid']],
            'name of 2 characters' => ['a@example.com', 'Al', 'secret-pass', 'secret-pass', ['name' => 'name_invalid']],
            'name of 33 characters' => ['a@example.com', str_repeat('a', 33), 'secret-pass', 'secret-pass', ['name' => 'name_invalid']],
            'name with a forbidden character' => ['a@example.com', '<script>', 'secret-pass', 'secret-pass', ['name' => 'name_invalid']],
            'name with a trailing newline' => ['a@example.com', "Player\n", 'secret-pass', 'secret-pass', ['name' => 'name_invalid']],
            'password of 7 characters' => ['a@example.com', 'Player', '1234567', '1234567', ['password' => 'password_too_short']],
            'password of 73 bytes' => ['a@example.com', 'Player', $longPassword, $longPassword, ['password' => 'password_too_long']],
            'different confirmation' => ['a@example.com', 'Player', 'secret-pass', 'secret-pasS', ['password_confirmation' => 'password_mismatch']],
        ];
    }

    /** @param array<string, string> $expectedErrors */
    #[DataProvider('rejectedRegistrations')]
    public function testInvalidRegistrationReturnsFieldErrorAndStoresNothing(
        string $email,
        string $name,
        string $password,
        string $confirmation,
        array $expectedErrors,
    ): void {
        $this->assertSame($expectedErrors, $this->authService->register($email, $name, $password, $confirmation, Locale::En));
        $this->assertSame(1, $this->repository->count());
    }

    public function testRegistrationWithTakenEmailInOtherCaseLooksLikeSuccess(): void
    {
        $this->assertSame([], $this->authService->register('PLAYER@example.com', 'Intruder', 'secret-pass', 'secret-pass', Locale::En));
    }

    public function testRegistrationWithTakenEmailKeepsExistingAccount(): void
    {
        $this->authService->register('PLAYER@example.com', 'Intruder', 'secret-pass', 'secret-pass', Locale::En);

        $this->assertSame($this->user, $this->repository->findByEmail('player@example.com'));
        $this->assertSame(1, $this->repository->count());
    }
}
