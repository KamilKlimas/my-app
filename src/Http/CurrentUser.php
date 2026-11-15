<?php

declare(strict_types=1);

namespace App\Http;

use App\Model\User;
use App\Repository\UserRepository;

final class CurrentUser
{
    public const string SESSION_KEY = 'user_id';

    private bool $loaded = false;
    private ?User $user = null;

    public function __construct(
        private readonly Session $session,
        private readonly UserRepository $users,
    ) {
    }

    public function user(): ?User
    {
        if (!$this->loaded) {
            $this->user = $this->load();
            $this->loaded = true;
        }

        return $this->user;
    }

    private function load(): ?User
    {
        $id = $this->session->int(self::SESSION_KEY);

        if ($id === null) {
            return null;
        }

        $user = $this->users->findById($id);

        if ($user === null) {
            $this->session->remove(self::SESSION_KEY);
        }

        return $user;
    }
}
