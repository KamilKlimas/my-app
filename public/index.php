<?php

declare(strict_types=1);

use App\Controller\AdminController;
use App\Controller\DefaultController;
use App\Controller\DemoController;
use App\Controller\HotspotController;
use App\Controller\ProjectController;
use App\Controller\SecurityController;
use App\Controller\TrainingController;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

$routing->get('/projects', ProjectController::class, 'index');
$routing->get('/projects/{id}', ProjectController::class, 'show');
$routing->get('/login', SecurityController::class, 'login');
$routing->get('/demos', DemoController::class, 'index');
$routing->get('/demos/{id}', DemoController::class, 'show');
$routing->get('/hotspots', HotspotController::class, 'index');
$routing->get('/training', TrainingController::class, 'index');
$routing->get('/admin', AdminController::class, 'index');
$routing->get('/', DefaultController::class, 'index');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    http_response_code(404);
    (new DefaultController())->notFound();
}