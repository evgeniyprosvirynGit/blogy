<?php

declare(strict_types=1);

namespace App\Support;

final class BlogDemoData
{
    /**
     * @return array<int, array{id: int, name: string, slug: string, description: string, created_at: string, updated_at: string}>
     */
    public static function categories(): array
    {
        return [
            self::category(1, 'Design Systems', 'design-systems', 'Layouts, UI patterns, and content presentation ideas for structured editorial pages.'),
            self::category(2, 'Frontend Engineering', 'frontend-engineering', 'Implementation notes, component patterns, and browser-facing engineering articles.'),
            self::category(3, 'Content Strategy', 'content-strategy', 'Editorial structure, archive management, and article discovery patterns.'),
            self::category(4, 'Product Thinking', 'product-thinking', 'Decision-making, product communication, and feature-shaping write-ups.'),
        ];
    }

    /**
     * @return array<int, array{
     *   id: int,
     *   image: string,
     *   title: string,
     *   slug: string,
     *   description: string,
     *   content: string,
     *   views: int,
     *   published_at: string,
     *   created_at: string,
     *   updated_at: string
     * }>
     */
    public static function posts(): array
    {
        return [
            self::post(1, '/images/blog.jpg', 'Building a category page that scales with editorial content', 'building-a-category-page', 'A practical layout study for category feeds with a stable hierarchy, readable summaries, and strong scanability.', 2430, '2026-07-21 09:00:00'),
            self::post(2, '/images/blogs.jpg', 'Editorial UX patterns for article archives', 'editorial-ux-patterns', 'How to balance sorting, pagination, and article density without turning the archive into a noisy dashboard.', 1870, '2026-07-18 09:00:00'),
            self::post(3, '/images/images.jpeg', 'Meaningful card layouts for content-heavy pages', 'meaningful-card-layouts', 'Examples of card composition that keep previews useful while preserving visual rhythm on desktop and mobile.', 1640, '2026-07-12 09:00:00'),
            self::post(4, '/images/blogs.jpg', 'Improving category navigation with simple hierarchy', 'improving-category-navigation', 'A compact approach to breadcrumbs, page titles, and supporting context that keeps readers oriented.', 1290, '2026-07-08 09:00:00'),
            self::post(5, '/images/blog.jpg', 'Archive pagination patterns that stay readable', 'archive-pagination', 'Static UI examples for pagination controls that communicate position and next steps clearly.', 986, '2026-07-03 09:00:00'),
            self::post(6, '/images/images.jpeg', 'Sort controls that do not dominate the page', 'sort-controls', 'A lightweight control bar pattern for article lists where sorting is useful but not the primary action.', 754, '2026-06-29 09:00:00'),
        ];
    }

    /**
     * @return array<int, array{post_id: int, category_id: int}>
     */
    public static function postCategories(): array
    {
        return [
            ['post_id' => 1, 'category_id' => 1],
            ['post_id' => 1, 'category_id' => 3],
            ['post_id' => 2, 'category_id' => 1],
            ['post_id' => 2, 'category_id' => 2],
            ['post_id' => 3, 'category_id' => 1],
            ['post_id' => 3, 'category_id' => 2],
            ['post_id' => 4, 'category_id' => 3],
            ['post_id' => 5, 'category_id' => 3],
            ['post_id' => 5, 'category_id' => 4],
            ['post_id' => 6, 'category_id' => 1],
            ['post_id' => 6, 'category_id' => 4],
        ];
    }

    /**
     * @return array{id: int, name: string, slug: string, description: string, created_at: string, updated_at: string}
     */
    private static function category(int $id, string $name, string $slug, string $description): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'created_at' => '2026-07-24 12:00:00',
            'updated_at' => '2026-07-24 12:00:00',
        ];
    }

    /**
     * @return array{
     *   id: int,
     *   image: string,
     *   title: string,
     *   slug: string,
     *   description: string,
     *   content: string,
     *   views: int,
     *   published_at: string,
     *   created_at: string,
     *   updated_at: string
     * }
     */
    private static function post(
        int $id,
        string $image,
        string $title,
        string $slug,
        string $description,
        int $views,
        string $publishedAt,
    ): array {
        return [
            'id' => $id,
            'image' => $image,
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'content' => implode("\n\n", [
                'The article page is where the visual language of the blog either holds together or falls apart.',
                'It needs enough structure to support long-form reading without turning into a wall of text.',
                'Related articles should extend the reading journey without competing with the main content.',
            ]),
            'views' => $views,
            'published_at' => $publishedAt,
            'created_at' => '2026-07-24 12:00:00',
            'updated_at' => '2026-07-24 12:00:00',
        ];
    }
}
