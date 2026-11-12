<?php

declare(strict_types=1);

use App\Controller\AdminController;
use App\Controller\DefaultController;
use App\Controller\DemoController;
use App\Controller\HotspotController;
use App\Controller\SecurityController;
use App\Controller\TrainingController;
use App\Http\BadRequest;
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
    $routeFound = $routing->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
    );
} catch (BadRequest) {
    http_response_code(400);
    (new DefaultController())->badRequest();

    return;
}

if (!$routeFound) {
    http_response_code(404);
    (new DefaultController())->notFound();
}