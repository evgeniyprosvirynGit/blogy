<?php

declare(strict_types=1);

use App\Models\Post;

beforeEach(function (): void {
    testDatabase();
});

it('builds article page data from the database', function (): void {
    $post = Post::articlePageDataBySlug('building-a-category-page');

    expect($post)->not->toBeNull()
        ->and($post)->toHaveKeys([
            'id',
            'title',
            'category',
            'category_ids',
            'image',
            'description',
            'publishedAt',
            'views',
            'paragraphs',
        ])
        ->and($post['title'])->toBe('Building a category page that scales with editorial content')
        ->and($post['category']['slug'])->toBe('design-systems')
        ->and($post['publishedAt'])->toBe('July 21, 2026')
        ->and($post['views'])->toBe('2,430')
        ->and($post['paragraphs'])->toHaveCount(3);
});

it('returns null when article page data is requested for a missing slug', function (): void {
    expect(Post::articlePageDataBySlug('missing-post'))->toBeNull();
});

it('builds preview cards for a category sorted by date by default', function (): void {
    $cards = Post::previewCardsForCategory(1);

    expect($cards['total'])->toBe(4)
        ->and($cards['items'])->toHaveCount(4)
        ->and($cards['current_page'])->toBe(1)
        ->and($cards['items'][0]['href'])->toBe('/post/building-a-category-page')
        ->and($cards['items'][1]['href'])->toBe('/post/editorial-ux-patterns');
});

it('builds preview cards for a category sorted by views when requested', function (): void {
    $cards = Post::previewCardsForCategory(1, 'views');

    expect($cards['total'])->toBe(4)
        ->and($cards['items'])->toHaveCount(4)
        ->and($cards['current_page'])->toBe(1)
        ->and($cards['items'][0]['href'])->toBe('/post/building-a-category-page')
        ->and($cards['items'][1]['href'])->toBe('/post/editorial-ux-patterns')
        ->and($cards['items'][0]['meta'])->toContain('2,430 views');
});

it('builds preview cards for a specific category page', function (): void {
    $cards = Post::previewCardsForCategory(1, 'published_at', 2, 2);

    expect($cards['total'])->toBe(4)
        ->and($cards['items'])->toHaveCount(2)
        ->and($cards['current_page'])->toBe(2)
        ->and($cards['items'][0]['href'])->toBe('/post/meaningful-card-layouts')
        ->and($cards['items'][1]['href'])->toBe('/post/sort-controls');
});

it('clamps requested category page to the last available page', function (): void {
    $cards = Post::previewCardsForCategory(1, 'published_at', 2, 999);

    expect($cards['total'])->toBe(4)
        ->and($cards['current_page'])->toBe(2)
        ->and($cards['items'])->toHaveCount(2)
        ->and($cards['items'][0]['href'])->toBe('/post/meaningful-card-layouts');
});

it('builds related preview cards excluding the current post', function (): void {
    $cards = Post::relatedPreviewCards([1, 3], 1, 3);

    expect($cards)->toHaveCount(3)
        ->and(collect($cards)->pluck('href')->all())->not->toContain('/post/building-a-category-page')
        ->and($cards[0]['href'])->toBe('/post/editorial-ux-patterns');
});
