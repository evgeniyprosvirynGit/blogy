<?php

declare(strict_types=1);

namespace App\Classes\RateLimiting;

final readonly class RateLimitDecision
{
    public function __construct(
        public bool $allowed,
        public int $delayMs,
        public int $retryAfter,
        public int $count,
    ) {
    }
}
