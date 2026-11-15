<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\BadRequest;
use App\Http\CurrentUser;
use App\Http\Query;
use App\Http\Redirect;
use App\Http\Session;
use App\I18n\Locale;
use App\Service\AccountService;

final class LocaleController extends AppController
{
    public function __construct(
        private readonly Session $session,
        private readonly CurrentUser $currentUser,
        private readonly AccountService $accountService,
    ) {
    }

    public function change(): void
    {
        $form = new Query($_POST);
        $locale = Locale::tryFrom($form->string('locale', ''));
        $returnTo = $form->string('return_to', '/');

        if ($locale === null) {
            throw new BadRequest('Unsupported locale');
        }

        $this->session->set(Locale::SESSION_KEY, $locale->value);
        $user = $this->currentUser->user();

        if ($user !== null) {
            $this->accountService->changeLocale($user->id, $locale);
        }

        throw new Redirect(Redirect::isLocalPath($returnTo) ? $returnTo : '/');
    }
}
