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
use App\Classes\Images\ResponsiveImageService;
use App\Classes\Cache\RedisCacheStore;
use App\Classes\Posts\RelatedArticlesProvider;
use App\Classes\RateLimiting\ArrayRateLimitStore;
use App\Classes\RateLimiting\RedisRateLimitStore;
use App\Classes\RateLimiting\RequestRateLimitGuard;
use App\Classes\RateLimiting\RequestRateLimiter;
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
$imagesConfig = require $basePath . '/config/images.php';
$rateLimitConfig = require $basePath . '/config/rate_limits.php';
$redisConfig = require $basePath . '/config/redis.php';

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? $_SERVER['APP_TIMEZONE'] ?? 'Asia/Tbilisi');

$view = new View($appConfig);
$errorLogger = new FileErrorLogger(
    $appConfig['paths']['logs']['error'],
    $appConfig['paths']['logs']['application'],
);
$errorHandler = new TemplateErrorHandler($view, $errorLogger);

try {
    Database::boot($databaseConfig);
} catch (\Throwable $exception) {
    echo $errorHandler->handle(ApplicationError::DATABASE_UNAVAILABLE, $exception);

    return;
}

$useRedisBackedServices = (($cacheConfig['driver'] ?? 'redis') === 'redis');

try {
    if ($useRedisBackedServices) {
        RedisConnection::boot($redisConfig);
        CacheManager::boot(new Cache(new RedisCacheStore()), $cacheConfig);
    } else {
        CacheManager::boot(new Cache(new ArrayCacheStore()), $cacheConfig);
    }
} catch (\Throwable) {
    $useRedisBackedServices = false;
    CacheManager::boot(new Cache(new ArrayCacheStore()), $cacheConfig);
}

$responsiveImageService = new ResponsiveImageService($imagesConfig);
$rateLimitStore = $useRedisBackedServices && (($rateLimitConfig['driver'] ?? 'redis') === 'redis')
    ? new RedisRateLimitStore()
    : new ArrayRateLimitStore();
$rateLimitGuard = new RequestRateLimitGuard(
    new RequestRateLimiter($rateLimitStore, $rateLimitConfig),
    $errorHandler,
);
$categoryPostSorter = new CategoryPostSorter();
$categoryPaginator = new CategoryPaginator();
$relatedArticlesProvider = new RelatedArticlesProvider($appConfig['blog']['article_page']['related_posts_limit']);
$homeController = new HomeController(
    $view,
    $errorHandler,
    $responsiveImageService,
    $appConfig['blog']['homepage']['page_title'],
    $appConfig['blog']['homepage']['categories_limit'],
    $appConfig['blog']['homepage']['posts_per_category'],
    $appConfig['blog']['homepage']['default_post_image'],
);
$categoryController = new CategoryController(
    $view,
    $errorHandler,
    $responsiveImageService,
    $appConfig['blog']['homepage']['default_post_image'],
    $appConfig['blog']['category_page']['posts_per_page'],
    $categoryPostSorter,
    $categoryPaginator,
);
$postController = new PostController(
    $view,
    $errorHandler,
    $responsiveImageService,
    $relatedArticlesProvider,
    $appConfig['blog']['homepage']['default_post_image'],
);
$router = new Router();

$router->get('/', static fn (): string => $rateLimitGuard->protect('homepage', static fn (): string => $homeController->index()));
$router->get('/category/{slug}', static fn (string $slug): string => $rateLimitGuard->protect('category', static fn (): string => $categoryController->show($slug)));
$router->get('/post/{slug}', static fn (string $slug): string => $rateLimitGuard->protect('post', static fn (): string => $postController->show($slug)));
$router->fallback(static fn (): string => $rateLimitGuard->protect('not_found', static fn (): string => $errorHandler->handle(ApplicationError::ROUTE_NOT_FOUND)));

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
