<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

return [
    'driver' => $_ENV['IMAGE_DRIVER'] ?? $_SERVER['IMAGE_DRIVER'] ?? 'magick',
    'cache_directory' => $basePath . '/public/images/cache',
    'cache_url_prefix' => '/images/cache',
    'presets' => [
        'home_card' => [
            'sizes' => [
                ['width' => 384, 'height' => 240],
                ['width' => 768, 'height' => 480],
            ],
            'sizes_attribute' => '(min-width: 768px) 384px, 100vw',
        ],
        'category_card' => [
            'sizes' => [
                ['width' => 420, 'height' => 260],
                ['width' => 840, 'height' => 520],
            ],
            'sizes_attribute' => '(min-width: 1280px) 420px, (min-width: 768px) 50vw, 100vw',
        ],
        'related_card' => [
            'sizes' => [
                ['width' => 360, 'height' => 220],
                ['width' => 720, 'height' => 440],
            ],
            'sizes_attribute' => '(min-width: 1280px) 360px, (min-width: 768px) 50vw, 100vw',
        ],
        'article_cover' => [
            'sizes' => [
                ['width' => 960, 'height' => 560],
                ['width' => 1600, 'height' => 920],
            ],
            'sizes_attribute' => '(min-width: 1024px) 960px, 100vw',
        ],
    ],
];
