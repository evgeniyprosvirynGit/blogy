<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Router
{
    /**
     * @var array<string, array<string, Closure>>
     */
    private array $routes = [];

    public function get(string $path, Closure $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): string
    {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?: '/');
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler instanceof Closure) {
            return $handler();
        }

        http_response_code(404);

        return 'Page not found';
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        $normalized = '/' . trim($path, '/');

        return $normalized === '/' ? $normalized : rtrim($normalized, '/');
    }
}
