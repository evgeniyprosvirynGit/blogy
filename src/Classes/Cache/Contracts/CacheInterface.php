<?php

declare(strict_types=1);

namespace App\Classes\Cache\Contracts;

interface CacheInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value, ?int $ttl = null): bool;

    public function remember(string $key, callable $callback, ?int $ttl = null): mixed;

    public function forget(string $key): bool;

    public function has(string $key): bool;

    public function clear(): bool;
}
