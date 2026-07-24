<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PostController;

beforeEach(function (): void {
    testDatabase();
});

it('renders the homepage with seeded categories and posts', function (): void {
    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        $config['blog']['homepage']['categories_limit'],
        $config['blog']['homepage']['posts_per_category'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('Simple PHP Blog')
        ->toContain('Design Systems')
        ->toContain('Frontend Engineering')
        ->toContain('/category/design-systems')
        ->toContain('/post/building-a-category-page');
});

it('renders an empty state on homepage when there are no categories', function (): void {
    \App\Models\Post::query()->delete();
    \App\Models\Category::query()->delete();

    $config = testAppConfig();
    $controller = new HomeController(
        testView(),
        testErrorHandler(),
        $config['blog']['homepage']['categories_limit'],
        $config['blog']['homepage']['posts_per_category'],
    );

    $html = $controller->index();

    expect($html)
        ->toContain('No categories published yet')
        ->toContain('The homepage is connected to the database, but no categories are available for display yet.');
});

it('renders the category page with sorting controls and article cards', function (): void {
    $config = testAppConfig();
    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        $config['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('design-systems');

    expect($html)
        ->toContain('Design Systems')
        ->toContain('4 articles')
        ->toContain('By publication date')
        ->toContain('By views')
        ->toContain('Building a category page that scales with editorial content')
        ->toContain('Jul 21, 2026')
        ->toContain('/post/editorial-ux-patterns');
});

it('renders the article page with full content and related articles', function (): void {
    $controller = new PostController(testView(), testErrorHandler(), testRelatedArticlesProvider());

    $html = $controller->show('building-a-category-page');

    expect($html)
        ->toContain('Building a category page that scales with editorial content')
        ->toContain('July 21, 2026')
        ->toContain('The article page is where the visual language of the blog either holds together or falls apart.')
        ->toContain('3 similar articles')
        ->toContain('2,430 views')
        ->toContain('Editorial UX patterns for article archives');
});

it('renders a not found page when category does not exist', function (): void {
    $config = testAppConfig();
    $controller = new CategoryController(
        testView(),
        testErrorHandler(),
        $config['blog']['category_page']['posts_per_page'],
        testCategoryPostSorter(),
        testCategoryPaginator(),
    );

    $html = $controller->show('missing-category');

    expect($html)
        ->toContain('Category not found')
        ->toContain('The requested category does not exist or has been removed.');
});

it('renders a not found page when article does not exist', function (): void {
    $controller = new PostController(testView(), testErrorHandler(), testRelatedArticlesProvider());

    $html = $controller->show('missing-article');

    expect($html)
        ->toContain('Article not found')
        ->toContain('The requested article does not exist or has been removed.');
});
