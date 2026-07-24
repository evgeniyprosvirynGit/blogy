<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Database\Capsule\Manager as Capsule;

final class Database
{
    private static ?Capsule $capsule = null;

    public static function boot(array $config): Capsule
    {
        if (self::$capsule instanceof Capsule) {
            return self::$capsule;
        }

        $capsule = new Capsule();
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if (filter_var($_ENV['DB_QUERY_LOG'] ?? $_SERVER['DB_QUERY_LOG'] ?? false, FILTER_VALIDATE_BOOL)) {
            self::registerQueryLogger($capsule);
        }

        self::$capsule = $capsule;

        return self::$capsule;
    }

    public static function reset(): void
    {
        if (self::$capsule instanceof Capsule) {
            self::$capsule->getConnection()->disconnect();
        }

        self::$capsule = null;
    }

    private static function registerQueryLogger(Capsule $capsule): void
    {
        $logPath = dirname(__DIR__, 2) . '/storage/logs/sql.log';
        $logDir = dirname($logPath);

        if (! is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        if (! is_file($logPath)) {
            touch($logPath);
        }

        $capsule->getConnection()->beforeExecuting(static function (string $sql, array $bindings) use ($logPath): void {
            $line = sprintf(
                "[%s] %s\n",
                date('Y-m-d H:i:s'),
                self::interpolateQuery($sql, $bindings),
            );

            file_put_contents($logPath, $line, FILE_APPEND);
        });
    }

    /**
     * @param array<int, mixed> $bindings
     */
    private static function interpolateQuery(string $sql, array $bindings): string
    {
        foreach ($bindings as $binding) {
            if ($binding === null) {
                $replacement = 'null';
            } elseif (is_bool($binding)) {
                $replacement = $binding ? '1' : '0';
            } elseif (is_numeric($binding)) {
                $replacement = (string) $binding;
            } else {
                $replacement = "'" . addslashes((string) $binding) . "'";
            }

            $sql = preg_replace('/\?/', $replacement, $sql, 1) ?? $sql;
        }

        return $sql;
    }
}
