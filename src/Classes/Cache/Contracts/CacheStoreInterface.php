<?php

declare(strict_types=1);

namespace App\Classes\Cache\Contracts;

interface CacheStoreInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, ?int $ttl = null): bool;

    public function delete(string $key): bool;

    public function has(string $key): bool;

    public function clear(): bool;
}
