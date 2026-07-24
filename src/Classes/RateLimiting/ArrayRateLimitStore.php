<?php

declare(strict_types=1);

namespace App\Classes\RateLimiting;

use App\Classes\RateLimiting\Contracts\RateLimitStoreInterface;

final class ArrayRateLimitStore implements RateLimitStoreInterface
{
    /**
     * @var array<string, int>
     */
    private array $counts = [];

    /**
     * @return array{count: int, reset_at: int}
     */
    public function hit(string $key, int $window, int $now): array
    {
        $bucket = intdiv($now, $window);
        $bucketKey = "{$key}:{$bucket}";
        $this->counts[$bucketKey] = ($this->counts[$bucketKey] ?? 0) + 1;

        return [
            'count' => $this->counts[$bucketKey],
            'reset_at' => (($bucket + 1) * $window),
        ];
    }
}
