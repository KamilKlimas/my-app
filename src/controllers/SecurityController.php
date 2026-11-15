<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Csrf;
use App\Http\CurrentUser;
use App\Http\Query;
use App\Http\Redirect;
use App\Http\Session;
use App\Service\AuthService;
use App\View\View;

final class SecurityController extends AppController
{
    public function __construct(
        private readonly View $view,
        private readonly AuthService $authService,
        private readonly Session $session,
        private readonly Csrf $csrf,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function home(): void
    {
        throw new Redirect($this->currentUser->user() === null ? '/login' : '/demos');
    }

    public function login(): void
    {
        $registered = (new Query($_GET))->string('registered', '') === '1';
        $this->renderForm('', null, $registered ? 'login.registered' : null);
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
            $this->renderForm($email, 'login.error.invalid_credentials');

            return;
        }

        $this->session->regenerate();
        $this->session->set(CurrentUser::SESSION_KEY, $user->id);
        $this->csrf->rotate();

        throw new Redirect('/demos');
    }

    public function logout(): void
    {
        $this->session->destroy();

        throw new Redirect('/login');
    }

    private function renderForm(string $email, ?string $errorKey, ?string $noticeKey = null): void
    {
        $this->view->render('login', [
            'pageTitle' => 'login.title',
            'email' => $email,
            'errorKey' => $errorKey,
            'noticeKey' => $noticeKey,
        ]);
    }

    /** @param array<string, string> $errorCodes */
    private function renderRegistrationForm(string $email, string $name, array $errorCodes): void
    {
        $this->view->render('register', [
            'pageTitle' => 'register.title',
            'email' => $email,
            'name' => $name,
            'errors' => $errorCodes,
        ]);
    }
}
