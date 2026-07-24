<?php

declare(strict_types=1);

use App\Classes\Errors\FileErrorLogger;
use App\Classes\Errors\TemplateErrorHandler;
use App\Classes\Errors\Enums\ApplicationError;

it('writes server errors to the error log file', function (): void {
    $errorLog = sys_get_temp_dir() . '/blogy-error-handler-error.log';
    $applicationLog = sys_get_temp_dir() . '/blogy-error-handler-application.log';

    if (is_file($errorLog)) {
        unlink($errorLog);
    }

    if (is_file($applicationLog)) {
        unlink($applicationLog);
    }

    $handler = new TemplateErrorHandler(
        testView(),
        new FileErrorLogger($errorLog, $applicationLog),
    );

    $html = $handler->handle(
        ApplicationError::ARTICLE_UNAVAILABLE,
        new \RuntimeException('Database timeout'),
    );

    expect($html)->toContain('Article unavailable')
        ->and(file_exists($errorLog))->toBeTrue()
        ->and(file_get_contents($errorLog))->toContain('"level":"error"')
        ->and(file_get_contents($errorLog))->toContain('"error":"article_unavailable"')
        ->and(file_get_contents($errorLog))->toContain('Database timeout');
});

it('writes not found errors to the application log file', function (): void {
    $errorLog = sys_get_temp_dir() . '/blogy-error-handler-error.log';
    $applicationLog = sys_get_temp_dir() . '/blogy-error-handler-application.log';

    if (is_file($errorLog)) {
        unlink($errorLog);
    }

    if (is_file($applicationLog)) {
        unlink($applicationLog);
    }

    $handler = new TemplateErrorHandler(
        testView(),
        new FileErrorLogger($errorLog, $applicationLog),
    );

    $html = $handler->handle(ApplicationError::ROUTE_NOT_FOUND);

    expect($html)->toContain('Page not found')
        ->and(file_exists($applicationLog))->toBeTrue()
        ->and(file_get_contents($applicationLog))->toContain('"level":"warning"')
        ->and(file_get_contents($applicationLog))->toContain('"error":"route_not_found"');
});
