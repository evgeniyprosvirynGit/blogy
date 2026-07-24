<?php

declare(strict_types=1);

namespace App\Classes\Cache;

use App\Classes\Cache\Contracts\CacheStoreInterface;

final class NullCacheStore implements CacheStoreInterface
{
    public function get(string $key): mixed
    {
        return null;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        return true;
    }

    public function delete(string $key): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        return false;
    }

    public function deleteByPrefix(string $prefix): bool
    {
        return true;
    }

    public function clear(): bool
    {
        return true;
    }
}
