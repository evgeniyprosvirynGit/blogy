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

it('increments article views when opening an article page payload', function (): void {
    $before = Post::query()->where('slug', 'building-a-category-page')->value('views');

    $post = Post::openArticlePageBySlug('building-a-category-page');
    $after = Post::query()->where('slug', 'building-a-category-page')->value('views');

    expect($post)->not->toBeNull()
        ->and($after)->toBe($before + 1)
        ->and($post['views'])->toBe(number_format((int) $after));
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
    $cards = Post::previewCardsForCategory(1, 'views_desc');

    expect($cards['total'])->toBe(4)
        ->and($cards['items'])->toHaveCount(4)
        ->and($cards['current_page'])->toBe(1)
        ->and($cards['items'][0]['href'])->toBe('/post/building-a-category-page')
        ->and($cards['items'][1]['href'])->toBe('/post/editorial-ux-patterns')
        ->and($cards['items'][0]['meta'])->toContain('2,430 views');
});

it('falls back to publication date sorting for invalid sort values', function (): void {
    $cards = Post::previewCardsForCategory(1, 'views desc; drop table posts');

    expect($cards['items'][0]['href'])->toBe('/post/building-a-category-page')
        ->and($cards['items'][1]['href'])->toBe('/post/editorial-ux-patterns');
});

it('builds preview cards for a category sorted by oldest publication date first', function (): void {
    $cards = Post::previewCardsForCategory(1, 'published_at_asc');

    expect($cards['items'][0]['href'])->toBe('/post/sort-controls')
        ->and($cards['items'][1]['href'])->toBe('/post/meaningful-card-layouts');
});

it('builds preview cards for a category sorted by least views first', function (): void {
    $cards = Post::previewCardsForCategory(1, 'views_asc');

    expect($cards['items'][0]['href'])->toBe('/post/sort-controls')
        ->and($cards['items'][1]['href'])->toBe('/post/meaningful-card-layouts');
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

it('returns related cards that share at least one category with the article when categories are provided', function (): void {
    $cards = Post::relatedPreviewCards([1, 3], 1, 3);

    expect(collect($cards)->pluck('href')->all())->toBe([
        '/post/editorial-ux-patterns',
        '/post/meaningful-card-layouts',
        '/post/improving-category-navigation',
    ]);
});

it('returns related cards without breaking when an article has no categories', function (): void {
    Post::query()->create([
        'title' => 'Unassigned post',
        'slug' => 'unassigned-post',
        'image' => '',
        'description' => '',
        'content' => 'Standalone content.',
        'views' => 0,
        'published_at' => '2026-07-24 09:00:00',
    ]);

    $post = Post::articlePageDataBySlug('unassigned-post');

    expect($post)->not->toBeNull()
        ->and($post['category'])->toBeNull()
        ->and($post['category_ids'])->toBe([]);

    $cards = Post::relatedPreviewCards([], (int) $post['id'], 3);

    expect($cards)->toHaveCount(3)
        ->and(collect($cards)->pluck('href')->all())->not->toContain('/post/unassigned-post');
});

it('returns formatted zero views for an article with no views', function (): void {
    Post::query()->create([
        'title' => 'Zero views article',
        'slug' => 'zero-views-article',
        'image' => '/images/blog.jpg',
        'description' => '',
        'content' => 'Zero views body.',
        'views' => 0,
        'published_at' => '2026-07-24 07:00:00',
    ]);

    $post = Post::articlePageDataBySlug('zero-views-article');

    expect($post)->not->toBeNull()
        ->and($post['views'])->toBe('0');
});

it('includes a post in every category it belongs to', function (): void {
    $post = Post::query()->create([
        'title' => 'Shared taxonomy article',
        'slug' => 'shared-taxonomy-article',
        'image' => '/images/blog.jpg',
        'description' => 'Visible in multiple categories.',
        'content' => 'Shared body.',
        'views' => 12,
        'published_at' => '2026-07-24 10:00:00',
    ]);

    $post->categories()->attach([1, 2]);

    $designSystems = Post::previewCardsForCategory(1, 'published_at');
    $frontendEngineering = Post::previewCardsForCategory(2, 'published_at');

    expect(collect($designSystems['items'])->pluck('href')->all())->toContain('/post/shared-taxonomy-article')
        ->and(collect($frontendEngineering['items'])->pluck('href')->all())->toContain('/post/shared-taxonomy-article');
});
