<?php

declare(strict_types=1);

use App\Classes\RateLimiting\ArrayRateLimitStore;
use App\Classes\RateLimiting\RequestRateLimiter;

function testRateLimitConfig(): array
{
    return [
        'profiles' => [
            'category' => [
                'limit' => 2,
                'window' => 60,
                'burst' => 2,
                'base_delay_ms' => 100,
                'max_delay_ms' => 250,
            ],
        ],
    ];
}

it('allows requests under the soft limit without delay', function (): void {
    $limiter = new RequestRateLimiter(new ArrayRateLimitStore(), testRateLimitConfig(), static fn (): int => 1_700_000_000);

    $decision = $limiter->decide('category', '127.0.0.1');

    expect($decision->allowed)->toBeTrue()
        ->and($decision->delayMs)->toBe(0)
        ->and($decision->count)->toBe(1);
});

it('applies a delay within the burst range', function (): void {
    $limiter = new RequestRateLimiter(new ArrayRateLimitStore(), testRateLimitConfig(), static fn (): int => 1_700_000_000);

    $limiter->decide('category', '127.0.0.1');
    $limiter->decide('category', '127.0.0.1');
    $decision = $limiter->decide('category', '127.0.0.1');

    expect($decision->allowed)->toBeTrue()
        ->and($decision->delayMs)->toBe(100)
        ->and($decision->count)->toBe(3);
});

it('blocks requests beyond the burst range', function (): void {
    $limiter = new RequestRateLimiter(new ArrayRateLimitStore(), testRateLimitConfig(), static fn (): int => 1_700_000_000);

    $limiter->decide('category', '127.0.0.1');
    $limiter->decide('category', '127.0.0.1');
    $limiter->decide('category', '127.0.0.1');
    $limiter->decide('category', '127.0.0.1');
    $decision = $limiter->decide('category', '127.0.0.1');

    expect($decision->allowed)->toBeFalse()
        ->and($decision->delayMs)->toBe(0)
        ->and($decision->retryAfter)->toBeGreaterThan(0);
});
