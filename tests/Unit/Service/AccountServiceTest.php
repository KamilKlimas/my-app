<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\I18n\Locale;
use App\Model\Role;
use App\Model\User;
use App\Service\AccountService;
use App\Tests\Unit\Fixture\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class AccountServiceTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private AccountService $accountService;

    protected function setUp(): void
    {
        $this->repository = new InMemoryUserRepository();
        $this->repository->add(new User(1, 'player@example.com', 'Player', 'hash', Role::User, Locale::Pl));
        $this->repository->add(new User(2, 'other@example.com', 'Other', 'hash', Role::User, Locale::Pl));
        $this->accountService = new AccountService($this->repository);
    }

    public function testChangeLocaleStoresNewLocaleForThatUser(): void
    {
        $this->accountService->changeLocale(1, Locale::En);

        $this->assertSame(Locale::En, $this->repository->findById(1)?->locale);
    }

    public function testChangeLocaleLeavesOtherUsersUnchanged(): void
    {
        $this->accountService->changeLocale(1, Locale::En);

        $this->assertSame(Locale::Pl, $this->repository->findById(2)?->locale);
    }

    public function testChangeLocaleForUnknownIdChangesNobody(): void
    {
        $this->accountService->changeLocale(99, Locale::En);

        $this->assertSame(Locale::Pl, $this->repository->findById(1)?->locale);
        $this->assertSame(Locale::Pl, $this->repository->findById(2)?->locale);
    }
}
