<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Post;

beforeEach(function (): void {
    testDatabase();
});

it('caches homepage categories query results', function (): void {
    $first = Category::forHomepage(4);
    Category::query()->delete();

    $second = Category::forHomepage(4);

    expect($first)->toHaveCount(4)
        ->and($second)->toHaveCount(4)
        ->and($second->pluck('slug')->all())->toBe($first->pluck('slug')->all());
});

it('caches category post previews by sort', function (): void {
    $first = Post::previewCardsForCategory(3, 'views', 12);
    Post::query()->delete();

    $second = Post::previewCardsForCategory(3, 'views', 12);

    expect($first['total'])->toBe(3)
        ->and($first['items'])->toHaveCount(3)
        ->and($second)->toBe($first);
});

it('caches article page payload by slug', function (): void {
    $first = Post::articlePageDataBySlug('building-a-category-page');
    Post::query()->delete();

    $second = Post::articlePageDataBySlug('building-a-category-page');

    expect($first)->not->toBeNull()
        ->and($second)->toBe($first);
});

it('invalidates cached homepage categories after category changes', function (): void {
    $first = Category::forHomepage(4);

    Category::query()->create([
        'name' => 'Analytics',
        'slug' => 'analytics',
        'description' => 'Metrics and reporting.',
    ]);

    $second = Category::forHomepage(10);

    expect($first)->toHaveCount(4)
        ->and($second->pluck('slug')->all())->toContain('analytics');
});

it('invalidates cached article payload after post changes', function (): void {
    $first = Post::articlePageDataBySlug('building-a-category-page');
    $post = Post::query()->where('slug', 'building-a-category-page')->firstOrFail();
    $post->update([
        'title' => 'Updated article title',
    ]);

    $second = Post::articlePageDataBySlug('building-a-category-page');

    expect($first)->not->toBeNull()
        ->and($first['title'])->toBe('Building a category page that scales with editorial content')
        ->and($second)->not->toBeNull()
        ->and($second['title'])->toBe('Updated article title');
});
