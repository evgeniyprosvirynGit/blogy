<?php

declare(strict_types=1);

use App\Core\Router;

it('dispatches registered get routes for normalized paths', function (): void {
    $router = new Router();
    $router->get('/posts', static fn (): string => 'posts page');

    expect($router->dispatch('GET', '/posts/?page=2'))->toBe('posts page');
});
