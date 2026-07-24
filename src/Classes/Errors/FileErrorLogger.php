<?php

declare(strict_types=1);

namespace App\Classes\Errors;

use App\Classes\Errors\Contracts\ErrorLoggerInterface;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Errors\Enums\LogLevel;
use Throwable;

final class FileErrorLogger implements ErrorLoggerInterface
{
    public function __construct(
        private readonly string $errorLogPath,
        private readonly string $applicationLogPath,
    )
    {
    }

    public function log(ApplicationError $error, LogLevel $level, ?Throwable $exception = null): void
    {
        $logPath = $level === LogLevel::ERROR ? $this->errorLogPath : $this->applicationLogPath;

        $this->ensureLogFileExists($logPath);

        $payload = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level->value,
            'error' => $error->value,
            'status_code' => $error->statusCode(),
            'title' => $error->title(),
            'message' => $error->message(),
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
        ];

        if ($exception !== null) {
            $payload['exception'] = [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        file_put_contents(
            $logPath,
            json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND,
        );
    }

    private function ensureLogFileExists(string $logPath): void
    {
        $directory = dirname($logPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (! is_file($logPath)) {
            touch($logPath);
        }
    }
}
