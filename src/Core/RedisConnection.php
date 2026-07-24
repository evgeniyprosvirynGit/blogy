<?php

declare(strict_types=1);

namespace App\Core;

use Redis;
use RuntimeException;

final class RedisConnection
{
    private static ?Redis $client = null;

    /**
     * @param array<string, mixed> $config
     */
    public static function boot(array $config): Redis
    {
        if (self::$client instanceof Redis) {
            return self::$client;
        }

        if (! class_exists(Redis::class)) {
            throw new RuntimeException('The php-redis extension is required to use RedisConnection.');
        }

        $client = new Redis();
        $connection = self::normalizedConfig($config);

        $connected = $client->connect(
            $connection['host'],
            $connection['port'],
            $connection['timeout'],
        );

        if ($connected !== true) {
            throw new RuntimeException('Unable to connect to Redis.');
        }

        if ($connection['password'] !== null && $connection['password'] !== '') {
            $client->auth($connection['password']);
        }

        $client->select($connection['database']);
        $client->setOption(Redis::OPT_READ_TIMEOUT, $connection['read_timeout']);

        if ($connection['prefix'] !== '') {
            $client->setOption(Redis::OPT_PREFIX, $connection['prefix']);
        }

        self::$client = $client;

        return self::$client;
    }

    public static function client(): Redis
    {
        if (! self::$client instanceof Redis) {
            throw new RuntimeException('Redis connection has not been booted.');
        }

        return self::$client;
    }

    public static function reset(): void
    {
        if (self::$client instanceof Redis) {
            self::$client->close();
        }

        self::$client = null;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{host: string, port: int, database: int, password: ?string, timeout: float, read_timeout: float, prefix: string}
     */
    private static function normalizedConfig(array $config): array
    {
        if (isset($config['url']) && is_string($config['url']) && $config['url'] !== '') {
            $parts = parse_url($config['url']);

            if ($parts !== false) {
                $database = isset($parts['path']) ? (int) ltrim($parts['path'], '/') : (int) ($config['database'] ?? 0);

                return [
                    'host' => (string) ($parts['host'] ?? $config['host'] ?? '127.0.0.1'),
                    'port' => (int) ($parts['port'] ?? $config['port'] ?? 6379),
                    'database' => $database,
                    'password' => isset($parts['pass']) ? (string) $parts['pass'] : (isset($config['password']) ? (string) $config['password'] : null),
                    'timeout' => (float) ($config['timeout'] ?? 2.5),
                    'read_timeout' => (float) ($config['read_timeout'] ?? 2.5),
                    'prefix' => (string) ($config['prefix'] ?? ''),
                ];
            }
        }

        return [
            'host' => (string) ($config['host'] ?? '127.0.0.1'),
            'port' => (int) ($config['port'] ?? 6379),
            'database' => (int) ($config['database'] ?? 0),
            'password' => isset($config['password']) ? (string) $config['password'] : null,
            'timeout' => (float) ($config['timeout'] ?? 2.5),
            'read_timeout' => (float) ($config['read_timeout'] ?? 2.5),
            'prefix' => (string) ($config['prefix'] ?? ''),
        ];
    }
}
