<?php

declare(strict_types=1);

return [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'db',
    'port' => (int) ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? 3306),
    'database' => $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? 'db',
    'username' => $_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? 'db',
    'password' => $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? 'db',
    'charset' => $_ENV['DB_CHARSET'] ?? $_SERVER['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
