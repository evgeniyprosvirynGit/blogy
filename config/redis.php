<?php

declare(strict_types=1);

return [
    'scheme' => $_ENV['REDIS_SCHEME'] ?? $_SERVER['REDIS_SCHEME'] ?? 'tcp',
    'host' => $_ENV['REDIS_HOST'] ?? $_SERVER['REDIS_HOST'] ?? '127.0.0.1',
    'port' => (int) ($_ENV['REDIS_PORT'] ?? $_SERVER['REDIS_PORT'] ?? 6379),
    'database' => (int) ($_ENV['REDIS_DB'] ?? $_SERVER['REDIS_DB'] ?? 0),
    'password' => $_ENV['REDIS_PASSWORD'] ?? $_SERVER['REDIS_PASSWORD'] ?? null,
    'timeout' => (float) ($_ENV['REDIS_TIMEOUT'] ?? $_SERVER['REDIS_TIMEOUT'] ?? 2.5),
    'read_timeout' => (float) ($_ENV['REDIS_READ_TIMEOUT'] ?? $_SERVER['REDIS_READ_TIMEOUT'] ?? 2.5),
    'prefix' => $_ENV['REDIS_PREFIX'] ?? $_SERVER['REDIS_PREFIX'] ?? 'blogy:',
    'url' => $_ENV['REDIS_URL'] ?? $_SERVER['REDIS_URL'] ?? null,
];
