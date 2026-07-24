<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

return [
    'name' => 'Blogy',
    'env' => $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'local',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOL),
    'url' => $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'https://blogy.ddev.site',
    'blog' => [
        'homepage' => [
            'categories_limit' => 4,
            'posts_per_category' => 3,
        ],
        'category_page' => [
            'posts_per_page' => 12,
        ],
        'article_page' => [
            'related_posts_limit' => 3,
        ],
    ],
    'vite' => [
        'entrypoint' => 'resources/js/app.js',
        'manifest_path' => '/public/build/.vite/manifest.json',
        'build_directory' => '/build',
        'dev_server_url' => $_ENV['VITE_DEV_SERVER_URL'] ?? $_SERVER['VITE_DEV_SERVER_URL'] ?? 'https://blogy.ddev.site:5173',
    ],
    'paths' => [
        'base' => $basePath,
        'templates' => $basePath . '/templates',
        'smarty' => [
            'compile' => $basePath . '/storage/smarty/compile',
            'cache' => $basePath . '/storage/smarty/cache',
        ],
        'logs' => [
            'error' => $_ENV['ERROR_LOG_PATH'] ?? $_SERVER['ERROR_LOG_PATH'] ?? $basePath . '/storage/logs/error.log',
            'application' => $_ENV['APPLICATION_LOG_PATH'] ?? $_SERVER['APPLICATION_LOG_PATH'] ?? $basePath . '/storage/logs/application.log',
        ],
    ],
];
