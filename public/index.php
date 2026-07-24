<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\PostController;
use App\Classes\Cache\ArrayCacheStore;
use App\Classes\Cache\Cache;
use App\Classes\Categories\CategoryPaginator;
use App\Classes\Categories\CategoryPostSorter;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Errors\FileErrorLogger;
use App\Classes\Cache\RedisCacheStore;
use App\Classes\Posts\RelatedArticlesProvider;
use App\Core\CacheManager;
use App\Classes\Errors\TemplateErrorHandler;
use App\Core\Database;
use App\Core\Env;
use App\Core\RedisConnection;
use App\Core\Router;
use App\Core\View;

require dirname(__DIR__) . '/vendor/autoload.php';

$basePath = dirname(__DIR__);

Env::load($basePath);

$appConfig = require $basePath . '/config/app.php';
$cacheConfig = require $basePath . '/config/cache.php';
$databaseConfig = require $basePath . '/config/database.php';
$redisConfig = require $basePath . '/config/redis.php';

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? $_SERVER['APP_TIMEZONE'] ?? 'Asia/Tbilisi');

Database::boot($databaseConfig);

try {
    if (($cacheConfig['driver'] ?? 'redis') === 'redis') {
        RedisConnection::boot($redisConfig);
        CacheManager::boot(new Cache(new RedisCacheStore()), $cacheConfig);
    } else {
        CacheManager::boot(new Cache(new ArrayCacheStore()), $cacheConfig);
    }
} catch (Throwable) {
    CacheManager::boot(new Cache(new ArrayCacheStore()), $cacheConfig);
}

$view = new View($appConfig);
$errorLogger = new FileErrorLogger(
    $appConfig['paths']['logs']['error'],
    $appConfig['paths']['logs']['application'],
);
$errorHandler = new TemplateErrorHandler($view, $errorLogger);
$categoryPostSorter = new CategoryPostSorter();
$categoryPaginator = new CategoryPaginator();
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
    $categoryPostSorter,
    $categoryPaginator,
);
$postController = new PostController($view, $errorHandler, $relatedArticlesProvider);
$router = new Router();

$router->get('/', static fn (): string => $homeController->index());
$router->get('/category/{slug}', static fn (string $slug): string => $categoryController->show($slug));
$router->get('/post/{slug}', static fn (string $slug): string => $postController->show($slug));
$router->fallback(static fn (): string => $errorHandler->handle(ApplicationError::ROUTE_NOT_FOUND));

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
