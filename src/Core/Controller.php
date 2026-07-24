<?php

declare(strict_types=1);

namespace App\Core;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Enums\ApplicationError;
use Throwable;

abstract class Controller
{
    public function __construct(
        protected View $view,
        private readonly ErrorHandlerInterface $errorHandler,
    )
    {
    }

    protected function render(string $template, array $data = []): string
    {
        return $this->view->render($template, $data);
    }

    protected function handleError(ApplicationError $error, ?Throwable $exception = null): string
    {
        return $this->errorHandler->handle($error, $exception);
    }

    protected function queryString(string $key): ?string
    {
        $value = $_GET[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    protected function positiveIntQuery(string $key, int $default = 1): int
    {
        $value = filter_var($this->queryString($key) ?? $default, FILTER_VALIDATE_INT);

        return $value !== false && $value > 0 ? $value : $default;
    }
}
