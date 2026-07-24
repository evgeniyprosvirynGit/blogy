<?php

declare(strict_types=1);

namespace App\Classes\Cache;

use App\Classes\Cache\Contracts\CacheStoreInterface;
use App\Core\RedisConnection;

final readonly class RedisCacheStore implements CacheStoreInterface
{
    public function __construct(
        private CachePayloadSerializer $serializer = new CachePayloadSerializer(),
    ) {
    }

    public function get(string $key): mixed
    {
        $value = RedisConnection::client()->get($key);

        if ($value === false) {
            return null;
        }

        return $this->serializer->deserialize($value);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $payload = $this->serializer->serialize($value);

        if ($ttl !== null && $ttl > 0) {
            return RedisConnection::client()->setex($key, $ttl, $payload);
        }

        return RedisConnection::client()->set($key, $payload);
    }

    public function delete(string $key): bool
    {
        return RedisConnection::client()->del($key) > 0;
    }

    public function has(string $key): bool
    {
        return RedisConnection::client()->exists($key) > 0;
    }

    public function clear(): bool
    {
        return RedisConnection::client()->flushDB();
    }
}
