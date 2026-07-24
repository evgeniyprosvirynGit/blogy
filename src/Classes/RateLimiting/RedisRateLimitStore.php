<?php

declare(strict_types=1);

namespace App\Classes\RateLimiting;

use App\Classes\RateLimiting\Contracts\RateLimitStoreInterface;
use App\Core\RedisConnection;

final class RedisRateLimitStore implements RateLimitStoreInterface
{
    /**
     * @return array{count: int, reset_at: int}
     */
    public function hit(string $key, int $window, int $now): array
    {
        $bucket = intdiv($now, $window);
        $resetAt = ($bucket + 1) * $window;
        $bucketKey = "{$key}:{$bucket}";
        $count = (int) RedisConnection::client()->incr($bucketKey);

        if ($count === 1) {
            RedisConnection::client()->expireAt($bucketKey, $resetAt);
        }

        return [
            'count' => $count,
            'reset_at' => $resetAt,
        ];
    }
}
