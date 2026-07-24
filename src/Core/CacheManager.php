<?php

declare(strict_types=1);

namespace App\Core;

use App\Classes\Cache\Cache;
use App\Classes\Cache\Contracts\CacheInterface;
use App\Classes\Cache\NullCacheStore;

final class CacheManager
{
    private static ?CacheInterface $cache = null;

    /**
     * @var array<string, mixed>
     */
    private static array $config = [];

    /**
     * @param array<string, mixed> $config
     */
    public static function boot(CacheInterface $cache, array $config = []): void
    {
        self::$cache = $cache;
        self::$config = $config;
    }

    public static function instance(): CacheInterface
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        self::$cache = new Cache(new NullCacheStore());

        return self::$cache;
    }

    public static function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        return self::instance()->remember($key, $callback, $ttl);
    }

    public static function clear(): bool
    {
        return self::instance()->clear();
    }

    public static function reset(): void
    {
        self::$cache = null;
        self::$config = [];
    }

    public static function ttl(string $key, int $default = 0): int
    {
        $ttl = self::$config['ttl'][$key] ?? $default;

        return is_int($ttl) ? $ttl : $default;
    }
}
