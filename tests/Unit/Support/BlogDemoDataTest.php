<?php

declare(strict_types=1);

use App\Support\BlogDemoData;

it('provides categories with unique slugs', function (): void {
    $categories = BlogDemoData::categories();
    $slugs = array_column($categories, 'slug');

    expect($categories)->toHaveCount(4)
        ->and($slugs)->toBeArray()
        ->and(array_unique($slugs))->toHaveCount(count($slugs));
});

it('provides posts with required fields and unique slugs', function (): void {
    $posts = BlogDemoData::posts();
    $slugs = array_column($posts, 'slug');

    expect($posts)->toHaveCount(6)
        ->and(array_unique($slugs))->toHaveCount(count($slugs));

    foreach ($posts as $post) {
        expect($post)
            ->toHaveKeys(['id', 'image', 'title', 'slug', 'description', 'content', 'views', 'published_at'])
            ->and($post['title'])->not->toBe('')
            ->and($post['content'])->not->toBe('')
            ->and($post['views'])->toBeInt();
    }
});

it('provides valid post category relationships', function (): void {
    $categoryIds = array_column(BlogDemoData::categories(), 'id');
    $postIds = array_column(BlogDemoData::posts(), 'id');
    $relations = BlogDemoData::postCategories();

    expect($relations)->toHaveCount(11);

    foreach ($relations as $relation) {
        expect($relation)
            ->toHaveKeys(['post_id', 'category_id'])
            ->and(in_array($relation['post_id'], $postIds, true))->toBeTrue()
            ->and(in_array($relation['category_id'], $categoryIds, true))->toBeTrue();
    }

    $relatedPostIds = array_values(array_unique(array_column($relations, 'post_id')));

    expect($relatedPostIds)->toHaveCount(count($postIds));
});
