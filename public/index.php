<?php

declare(strict_types=1);

use App\Controller\AdminController;
use App\Controller\DefaultController;
use App\Controller\DemoController;
use App\Controller\HotspotController;
use App\Controller\LocaleController;
use App\Controller\SecurityController;
use App\Controller\TrainingController;
use App\Database\ConnectionFactory;
use App\Database\ConnectionFailed;
use App\Database\DatabaseConfig;
use App\Http\Access;
use App\Http\AccessGuard;
use App\Http\Csrf;
use App\Http\CurrentUser;
use App\Http\HttpException;
use App\Http\NotFound;
use App\Http\Redirect;
use App\Http\ServiceUnavailable;
use App\Http\Session;
use App\I18n\Locale;
use App\I18n\Translator;
use App\Repository\PdoUserRepository;
use App\Routing;
use App\Service\AccountService;
use App\Service\AuthService;
use App\View\View;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $session = Session::start(Session::secureFromEnvironment(getenv()));
    $csrf = new Csrf($session);
    $csrf->ensureToken();
    $locale = Locale::resolve($session->string(Locale::SESSION_KEY), $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null);
    $view = new View(Translator::forLocale($locale), $csrf, $_SERVER['REQUEST_URI']);
    $csrf->verifyRequest($_SERVER['REQUEST_METHOD'], $_POST);
    $database = (new ConnectionFactory())->create(DatabaseConfig::fromEnvironment(getenv()));

    $userRepository = new PdoUserRepository($database);
    $authService = new AuthService($userRepository);
    $currentUser = new CurrentUser($session, $userRepository);
    $securityController = fn (): SecurityController => new SecurityController(
        $view,
        $authService,
        $session,
        $csrf,
        $currentUser,
        $locale,
    );
    $accountService = new AccountService($userRepository);
    $localeController = fn (): LocaleController => new LocaleController($session, $currentUser, $accountService);

    $demoController = fn (): DemoController => new DemoController($view, $currentUser);
    $hotspotController = fn (): HotspotController => new HotspotController($view, $currentUser);
    $trainingController = fn (): TrainingController => new TrainingController($view, $currentUser);
    $adminController = fn (): AdminController => new AdminController($view, $currentUser);

    $routing = new Routing(new AccessGuard($currentUser));
    $routing->get('/login', $securityController, 'login', Access::Guest);
    $routing->post('/login', $securityController, 'authenticate', Access::Guest);
    $routing->get('/register', $securityController, 'register', Access::Guest);
    $routing->post('/register', $securityController, 'createAccount', Access::Guest);
    $routing->post('/logout', $securityController, 'logout', Access::Anyone);
    $routing->post('/locale', $localeController, 'change', Access::Anyone);
    $routing->get('/demos', $demoController, 'index', Access::User);
    $routing->get('/demos/{id}', $demoController, 'show', Access::User);
    $routing->get('/hotspots', $hotspotController, 'index', Access::User);
    $routing->get('/training', $trainingController, 'index', Access::User);
    $routing->get('/admin', $adminController, 'index', Access::Admin);
    $routing->get('/', $securityController, 'home', Access::Anyone);

    $routeFound = $routing->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
    );

    if (!$routeFound) {
        throw new NotFound();
    }
} catch (Redirect $redirect) {
    header('Location: ' . $redirect->path(), true, 303);
} catch (HttpException $error) {
    (new DefaultController($view))->error($error);
} catch (ConnectionFailed) {
    (new DefaultController($view))->error(new ServiceUnavailable());
}