<?php

declare(strict_types=1);

namespace App\Classes\RateLimiting;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Enums\ApplicationError;

final readonly class RequestRateLimitGuard
{
    public function __construct(
        private RequestRateLimiter $rateLimiter,
        private ErrorHandlerInterface $errorHandler,
    ) {
    }

    public function protect(string $profile, callable $handler): string
    {
        $decision = $this->rateLimiter->decide($profile, $this->requestIdentifier());

        if ($decision->delayMs > 0) {
            usleep($decision->delayMs * 1000);
        }

        if (! $decision->allowed) {
            header('Retry-After: ' . $decision->retryAfter);

            return $this->errorHandler->handle(ApplicationError::RATE_LIMITED);
        }

        return $handler();
    }

    private function requestIdentifier(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'cli';
    }
}
