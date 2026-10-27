<?php

declare(strict_types=1);

namespace App\Controller;

final class SecurityController extends AppController
{
    public function login(): void
    {
        $this->render('login', [
            'pageTitle' => 'Logowanie',
            'lang' => 'pl',
            'email' => '',
            'error' => isset($_GET['error']) ? 'Nieprawidłowy e-mail lub hasło.' : null,
        ]);
    }
}
