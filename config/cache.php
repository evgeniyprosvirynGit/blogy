<?php

declare(strict_types=1);

return [
    'driver' => $_ENV['CACHE_DRIVER'] ?? $_SERVER['CACHE_DRIVER'] ?? 'redis',
    'ttl' => [
        'homepage_categories' => (int) ($_ENV['CACHE_TTL_HOMEPAGE_CATEGORIES'] ?? $_SERVER['CACHE_TTL_HOMEPAGE_CATEGORIES'] ?? 900),
        'homepage_posts' => (int) ($_ENV['CACHE_TTL_HOMEPAGE_POSTS'] ?? $_SERVER['CACHE_TTL_HOMEPAGE_POSTS'] ?? 600),
        'category' => (int) ($_ENV['CACHE_TTL_CATEGORY'] ?? $_SERVER['CACHE_TTL_CATEGORY'] ?? 1800),
        'category_posts' => (int) ($_ENV['CACHE_TTL_CATEGORY_POSTS'] ?? $_SERVER['CACHE_TTL_CATEGORY_POSTS'] ?? 600),
        'article' => (int) ($_ENV['CACHE_TTL_ARTICLE'] ?? $_SERVER['CACHE_TTL_ARTICLE'] ?? 1800),
        'related_posts' => (int) ($_ENV['CACHE_TTL_RELATED_POSTS'] ?? $_SERVER['CACHE_TTL_RELATED_POSTS'] ?? 600),
    ],
];
