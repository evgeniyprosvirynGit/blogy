<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PostController;

it('renders the homepage with seeded categories and posts', function (): void {
    $controller = new HomeController(testView());

    $html = $controller->index();

    expect($html)
        ->toContain('Simple PHP Blog')
        ->toContain('Design Systems')
        ->toContain('Frontend Engineering')
        ->toContain('/category/design-systems')
        ->toContain('/post/building-a-category-page');
});

it('renders the category page with sorting controls and article cards', function (): void {
    $controller = new CategoryController(testView());

    $html = $controller->show('design-systems');

    expect($html)
        ->toContain('Design Systems')
        ->toContain('By publication date')
        ->toContain('By views')
        ->toContain('Building a category page that scales with editorial content')
        ->toContain('/post/editorial-ux-patterns');
});

it('renders the article page with full content and related articles', function (): void {
    $controller = new PostController(testView());

    $html = $controller->show('building-a-category-page');

    expect($html)
        ->toContain('Building a category page that scales with editorial content')
        ->toContain('James Carter')
        ->toContain('Why the article page matters')
        ->toContain('3 similar articles')
        ->toContain('Editorial UX patterns for article archives');
});
