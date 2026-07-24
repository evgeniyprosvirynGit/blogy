<?php

declare(strict_types=1);

namespace App\Classes\Errors\Contracts;

use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Errors\Enums\LogLevel;
use Throwable;

interface ErrorLoggerInterface
{
    public function log(ApplicationError $error, LogLevel $level, ?Throwable $exception = null): void;
}
