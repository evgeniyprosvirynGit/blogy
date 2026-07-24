<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Core\Database;
use App\Core\Env;
use App\Core\Router;
use App\Core\View;

require dirname(__DIR__) . '/vendor/autoload.php';

$basePath = dirname(__DIR__);

Env::load($basePath);

$appConfig = require $basePath . '/config/app.php';
$databaseConfig = require $basePath . '/config/database.php';

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? $_SERVER['APP_TIMEZONE'] ?? 'Asia/Tbilisi');

Database::boot($databaseConfig);

$view = new View($appConfig);
$homeController = new HomeController($view);
$router = new Router();

$router->get('/', static fn (): string => $homeController->index());

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
