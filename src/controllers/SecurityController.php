<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Csrf;
use App\Http\Query;
use App\Http\Redirect;
use App\Http\Session;
use App\Service\AuthService;

final class SecurityController extends AppController
{
    private const array REGISTRATION_ERRORS = [
        'email_invalid' => 'Podaj poprawny adres e-mail (do 254 znaków).',
        'name_invalid' => 'Nazwa: 3–32 znaki, litery, cyfry, spacja, kropka, myślnik lub podkreślnik.',
        'password_too_short' => 'Hasło musi mieć co najmniej 8 znaków.',
        'password_too_long' => 'Hasło jest za długie (najwyżej 72 bajty, czyli ok. 72 znaki bez polskich liter).',
        'password_mismatch' => 'Hasła nie są takie same.',
    ];

    public function __construct(
        private readonly AuthService $authService,
        private readonly Session $session,
        private readonly Csrf $csrf,
    ) {
    }

    public function login(): void
    {
        $registered = (new Query($_GET))->string('registered', '') === '1';
        $this->renderForm('', null, $registered ? 'Rejestracja przyjęta. Możesz się zalogować.' : null);
    }

    public function register(): void
    {
        $this->renderRegistrationForm('', '', []);
    }

    public function createAccount(): void
    {
        $form = new Query($_POST);
        $email = trim($form->string('email', ''));
        $name = trim($form->string('name', ''));
        $errors = $this->authService->register(
            $email,
            $name,
            $form->string('password', ''),
            $form->string('password_confirmation', ''),
        );

        if ($errors !== []) {
            http_response_code(422);
            $this->renderRegistrationForm($email, $name, $errors);

            return;
        }

        throw new Redirect('/login?registered=1');
    }

    public function authenticate(): void
    {
        $form = new Query($_POST);
        $email = trim($form->string('email', ''));
        $user = $this->authService->authenticate($email, $form->string('password', ''));

        if ($user === null) {
            http_response_code(422);
            $this->renderForm($email, 'Nieprawidłowy e-mail lub hasło.');

            return;
        }

        $this->session->regenerate();
        $this->session->set('user_id', $user->id);
        $this->csrf->rotate();

        throw new Redirect('/demos');
    }

    public function logout(): void
    {
        $this->session->destroy();

        throw new Redirect('/login');
    }

    private function renderForm(string $email, ?string $error, ?string $notice = null): void
    {
        $this->render('login', [
            'pageTitle' => 'Logowanie',
            'lang' => 'pl',
            'email' => $email,
            'error' => $error,
            'notice' => $notice,
        ]);
    }

    /** @param array<string, string> $errorCodes */
    private function renderRegistrationForm(string $email, string $name, array $errorCodes): void
    {
        $this->render('register', [
            'pageTitle' => 'Rejestracja',
            'lang' => 'pl',
            'email' => $email,
            'name' => $name,
            'errors' => array_map(fn (string $code): string => self::REGISTRATION_ERRORS[$code], $errorCodes),
        ]);
    }
}
