<?php

declare(strict_types=1);

namespace App\Classes\RateLimiting;

use Closure;
use App\Classes\RateLimiting\Contracts\RateLimitStoreInterface;

final class RequestRateLimiter
{
    /**
     * @param array<string, mixed> $config
     * @param Closure():int|null $nowProvider
     */
    public function __construct(
        private readonly RateLimitStoreInterface $store,
        private readonly array $config,
        private readonly ?Closure $nowProvider = null,
    ) {
    }

    public function decide(string $profile, string $identifier): RateLimitDecision
    {
        $profileConfig = $this->config['profiles'][$profile] ?? [
            'limit' => 60,
            'window' => 60,
            'burst' => 10,
            'base_delay_ms' => 100,
            'max_delay_ms' => 300,
        ];

        $now = $this->now();
        $hit = $this->store->hit("rate_limit:{$profile}:{$identifier}", (int) $profileConfig['window'], $now);
        $count = $hit['count'];
        $limit = (int) $profileConfig['limit'];
        $burst = (int) $profileConfig['burst'];
        $retryAfter = max(1, $hit['reset_at'] - $now);

        if ($count <= $limit) {
            return new RateLimitDecision(true, 0, $retryAfter, $count);
        }

        if ($count <= ($limit + $burst)) {
            $excess = $count - $limit;
            $delay = min(
                (int) $profileConfig['max_delay_ms'],
                (int) $profileConfig['base_delay_ms'] * $excess,
            );

            return new RateLimitDecision(true, $delay, $retryAfter, $count);
        }

        return new RateLimitDecision(false, 0, $retryAfter, $count);
    }

    private function now(): int
    {
        if ($this->nowProvider !== null) {
            return (int) ($this->nowProvider)();
        }

        return time();
    }
}
