<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Router
{
    /**
     * @var array<string, array<int, array{path: string, handler: Closure}>>
     */
    private array $routes = [];

    public function get(string $path, Closure $handler): void
    {
        $this->routes['GET'][] = [
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): string
    {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?: '/');
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            $parameters = $this->match($route['path'], $path);

            if ($parameters !== null) {
                return $route['handler'](...$parameters);
            }
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

    /**
     * @return array<int, string>|null
     */
    private function match(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace_callback(
            '/\{[^\/]+\}/',
            static fn (array $matches): string => '__ROUTE_PARAM__',
            $routePath,
        );

        if ($pattern === null) {
            return null;
        }

        $pattern = preg_quote($pattern, '/');
        $pattern = str_replace('__ROUTE_PARAM__', '([^\/]+)', $pattern);

        if (! preg_match('/^' . $pattern . '$/', $requestPath, $matches)) {
            return null;
        }

        array_shift($matches);

        return array_map('urldecode', $matches);
    }
}
