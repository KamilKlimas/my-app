<?php

declare(strict_types=1);

use App\Controller\AdminController;
use App\Controller\DefaultController;
use App\Controller\DemoController;
use App\Controller\HotspotController;
use App\Controller\SecurityController;
use App\Controller\TrainingController;
use App\Database\ConnectionFactory;
use App\Database\ConnectionFailed;
use App\Database\DatabaseConfig;
use App\Http\HttpException;
use App\Http\NotFound;
use App\Http\Redirect;
use App\Http\ServiceUnavailable;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

$routing->get('/login', SecurityController::class, 'login');
$routing->get('/demos', DemoController::class, 'index');
$routing->get('/demos/{id}', DemoController::class, 'show');
$routing->get('/hotspots', HotspotController::class, 'index');
$routing->get('/training', TrainingController::class, 'index');
$routing->get('/admin', AdminController::class, 'index');
$routing->get('/', DemoController::class, 'upload');

try {
    $database = (new ConnectionFactory())->create(DatabaseConfig::fromEnvironment(getenv()));

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
    (new DefaultController())->error($error);
} catch (ConnectionFailed) {
    (new DefaultController())->error(new ServiceUnavailable());
}