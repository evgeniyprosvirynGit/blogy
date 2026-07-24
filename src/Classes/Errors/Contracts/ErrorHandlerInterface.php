<?php

declare(strict_types=1);

namespace App\Classes\Errors\Contracts;

use App\Classes\Errors\Enums\ApplicationError;
use Throwable;

interface ErrorHandlerInterface
{
    public function handle(ApplicationError $error, ?Throwable $exception = null): string;
}
