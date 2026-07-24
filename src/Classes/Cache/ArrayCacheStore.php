<?php

declare(strict_types=1);

namespace App\Classes\Cache;

use App\Classes\Cache\Contracts\CacheStoreInterface;

final class ArrayCacheStore implements CacheStoreInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $items = [];

    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->items[$key] = $value;

        return true;
    }

    public function delete(string $key): bool
    {
        $exists = array_key_exists($key, $this->items);
        unset($this->items[$key]);

        return $exists;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function deleteByPrefix(string $prefix): bool
    {
        $deleted = false;

        foreach (array_keys($this->items) as $key) {
            if (! str_starts_with($key, $prefix)) {
                continue;
            }

            unset($this->items[$key]);
            $deleted = true;
        }

        return $deleted;
    }

    public function clear(): bool
    {
        $this->items = [];

        return true;
    }
}
