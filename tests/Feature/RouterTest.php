<?php

declare(strict_types=1);

use App\Core\Router;

it('dispatches registered get routes for normalized paths', function (): void {
    $router = new Router();
    $router->get('/posts', static fn (): string => 'posts page');

    expect($router->dispatch('GET', '/posts/?page=2'))->toBe('posts page');
});

it('dispatches registered get routes with path parameters', function (): void {
    $router = new Router();
    $router->get('/category/{slug}', static fn (string $slug): string => "category {$slug}");

    expect($router->dispatch('GET', '/category/design-systems'))->toBe('category design-systems');
});
