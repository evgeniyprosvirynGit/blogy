<?php

declare(strict_types=1);

return [
    'driver' => $_ENV['RATE_LIMIT_DRIVER'] ?? $_SERVER['RATE_LIMIT_DRIVER'] ?? 'redis',
    'profiles' => [
        'homepage' => [
            'limit' => 100,
            'window' => 60,
            'burst' => 20,
            'base_delay_ms' => 100,
            'max_delay_ms' => 300,
        ],
        'category' => [
            'limit' => 150,
            'window' => 60,
            'burst' => 30,
            'base_delay_ms' => 100,
            'max_delay_ms' => 400,
        ],
        'post' => [
            'limit' => 180,
            'window' => 60,
            'burst' => 40,
            'base_delay_ms' => 100,
            'max_delay_ms' => 400,
        ],
        'not_found' => [
            'limit' => 40,
            'window' => 60,
            'burst' => 10,
            'base_delay_ms' => 150,
            'max_delay_ms' => 500,
        ],
    ],
];
