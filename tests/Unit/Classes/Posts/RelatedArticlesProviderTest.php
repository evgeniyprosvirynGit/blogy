<?php

declare(strict_types=1);

use App\Classes\Posts\RelatedArticlesProvider;
use App\Models\Post;

beforeEach(function (): void {
    testDatabase();
});

it('returns related articles for an article using configured limit', function (): void {
    $article = Post::articlePageDataBySlug('building-a-category-page');

    expect($article)->not->toBeNull();

    $provider = new RelatedArticlesProvider(3);
    $cards = $provider->forArticle($article);

    expect($cards)->toHaveCount(3)
        ->and(collect($cards)->pluck('href')->all())->not->toContain('/post/building-a-category-page')
        ->and($cards[0]['href'])->toBe('/post/editorial-ux-patterns');
});
