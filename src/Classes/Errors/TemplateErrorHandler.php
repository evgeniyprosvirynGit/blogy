<?php

declare(strict_types=1);

namespace App\Classes\Errors;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Contracts\ErrorLoggerInterface;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Errors\Enums\LogLevel;
use App\Core\View;
use Throwable;

final class TemplateErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private readonly View $view,
        private readonly ErrorLoggerInterface $errorLogger,
    )
    {
    }

    public function handle(ApplicationError $error, ?Throwable $exception = null): string
    {
        $this->errorLogger->log($error, $this->logLevelFor($error), $exception);

        http_response_code($error->statusCode());

        return $this->view->render('errors/show.tpl', [
            'pageTitle' => $error->title(),
            'statusCode' => $error->statusCode(),
            'title' => $error->title(),
            'message' => $error->message(),
        ]);
    }

    private function logLevelFor(ApplicationError $error): LogLevel
    {
        return $error->statusCode() >= 500 ? LogLevel::ERROR : LogLevel::WARNING;
    }
}
