<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\PostController;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Errors\FileErrorLogger;
use App\Classes\Posts\RelatedArticlesProvider;
use App\Classes\Errors\TemplateErrorHandler;
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
$errorLogger = new FileErrorLogger(
    $appConfig['paths']['logs']['error'],
    $appConfig['paths']['logs']['application'],
);
$errorHandler = new TemplateErrorHandler($view, $errorLogger);
$relatedArticlesProvider = new RelatedArticlesProvider($appConfig['blog']['article_page']['related_posts_limit']);
$homeController = new HomeController(
    $view,
    $errorHandler,
    $appConfig['blog']['homepage']['categories_limit'],
    $appConfig['blog']['homepage']['posts_per_category'],
);
$categoryController = new CategoryController(
    $view,
    $errorHandler,
    $appConfig['blog']['category_page']['posts_per_page'],
);
$postController = new PostController($view, $errorHandler, $relatedArticlesProvider);
$router = new Router();

$router->get('/', static fn (): string => $homeController->index());
$router->get('/category/{slug}', static fn (string $slug): string => $categoryController->show($slug));
$router->get('/post/{slug}', static fn (string $slug): string => $postController->show($slug));
$router->fallback(static fn (): string => $errorHandler->handle(ApplicationError::ROUTE_NOT_FOUND));

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
