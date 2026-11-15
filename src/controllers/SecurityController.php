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
    public function __construct(
        private readonly AuthService $authService,
        private readonly Session $session,
        private readonly Csrf $csrf,
    ) {
    }

    public function login(): void
    {
        $this->renderForm('', null);
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

    private function renderForm(string $email, ?string $error): void
    {
        $this->render('login', [
            'pageTitle' => 'Logowanie',
            'lang' => 'pl',
            'email' => $email,
            'error' => $error,
        ]);
    }
}
