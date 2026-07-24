<?php

declare(strict_types=1);

use App\Core\Env;

require __DIR__ . '/vendor/autoload.php';

Env::load(__DIR__);

return [
    'paths' => [
        'migrations' => __DIR__ . '/database/migrations',
        'seeds' => __DIR__ . '/database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $_ENV['APP_ENV'] ?? 'local',
        'local' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? 'db',
            'name' => $_ENV['DB_DATABASE'] ?? 'db',
            'user' => $_ENV['DB_USERNAME'] ?? 'db',
            'pass' => $_ENV['DB_PASSWORD'] ?? 'db',
            'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? 'db',
            'name' => $_ENV['DB_DATABASE'] ?? 'db',
            'user' => $_ENV['DB_USERNAME'] ?? 'db',
            'pass' => $_ENV['DB_PASSWORD'] ?? 'db',
            'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
    'version_order' => 'creation',
];
