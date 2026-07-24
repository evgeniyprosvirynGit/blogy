<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;

final class Env
{
    public static function load(string $basePath): void
    {
        if (! is_file($basePath . '/.env')) {
            return;
        }

        Dotenv::createImmutable($basePath)->safeLoad();
    }
}
