<?php

declare(strict_types=1);

namespace App\Classes\Cache;

use App\Classes\Cache\Contracts\CacheInterface;
use App\Classes\Cache\Contracts\CacheStoreInterface;

final readonly class Cache implements CacheInterface
{
    public function __construct(
        private CacheStoreInterface $store,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->store->get($key);

        return $value ?? $default;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        return $this->store->set($key, $value, $ttl);
    }

    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $value = $this->store->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->store->set($key, $value, $ttl);

        return $value;
    }

    public function forget(string $key): bool
    {
        return $this->store->delete($key);
    }

    public function has(string $key): bool
    {
        return $this->store->has($key);
    }

    public function forgetByPrefix(string $prefix): bool
    {
        return $this->store->deleteByPrefix($prefix);
    }

    public function clear(): bool
    {
        return $this->store->clear();
    }
}
