<?php

declare(strict_types=1);

namespace App\Classes\RateLimiting\Contracts;

interface RateLimitStoreInterface
{
    /**
     * @return array{count: int, reset_at: int}
     */
    public function hit(string $key, int $window, int $now): array;
}
