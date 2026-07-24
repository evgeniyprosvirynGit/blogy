<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class CategoryController extends Controller
{
    public function show(string $slug): string
    {
        $categoryName = ucwords(str_replace('-', ' ', $slug));

        return $this->render('category/show.tpl', [
            'pageTitle' => $categoryName,
            'category' => [
                'name' => $categoryName,
                'slug' => $slug,
                'description' => 'Curated articles, practical notes, and editor picks collected in one category feed layout.',
                'articleCount' => 12,
            ],
            'sortOptions' => [
                [
                    'label' => 'By publication date',
                    'value' => 'published_at',
                    'href' => "/category/{$slug}?sort=published_at",
                ],
                [
                    'label' => 'By views',
                    'value' => 'views',
                    'href' => "/category/{$slug}?sort=views",
                ],
            ],
            'currentSort' => 'published_at',
            'posts' => [
                [
                    'href' => '/post/building-a-category-page',
                    'image' => '/images/blog.jpg',
                    'title' => 'Building a category page that scales with editorial content',
                    'description' => 'A practical layout study for category feeds with a stable hierarchy, readable summaries, and strong scanability.',
                    'badges' => ['Editorial', 'Staff Pick'],
                    'author' => 'James',
                    'date' => 'Jul 21, 2026',
                ],
                [
                    'href' => '/post/editorial-ux-patterns',
                    'image' => '/images/blogs.jpg',
                    'title' => 'Editorial UX patterns for article archives',
                    'description' => 'How to balance sorting, pagination, and article density without turning the archive into a noisy dashboard.',
                    'badges' => ['Mindfulness', 'Staff Pick'],
                    'author' => 'James',
                    'date' => 'Jul 18, 2026',
                ],
                [
                    'href' => '/post/meaningful-card-layouts',
                    'image' => '/images/images.jpeg',
                    'title' => 'Meaningful card layouts for content-heavy pages',
                    'description' => 'Examples of card composition that keep previews useful while preserving visual rhythm on desktop and mobile.',
                    'badges' => ['Lifestyle'],
                    'author' => 'James',
                    'date' => 'Jul 12, 2026',
                ],
                [
                    'href' => '/post/improving-category-navigation',
                    'image' => '/images/blogs.jpg',
                    'title' => 'Improving category navigation with simple hierarchy',
                    'description' => 'A compact approach to breadcrumbs, page titles, and supporting context that keeps readers oriented.',
                    'badges' => ['Mindfulness', 'Staff Pick'],
                    'author' => 'James',
                    'date' => 'Jul 8, 2026',
                ],
                [
                    'href' => '/post/archive-pagination',
                    'image' => '/images/blog.jpg',
                    'title' => 'Archive pagination patterns that stay readable',
                    'description' => 'Static UI examples for pagination controls that communicate position and next steps clearly.',
                    'badges' => ['Meditation'],
                    'author' => 'James',
                    'date' => 'Jul 3, 2026',
                ],
                [
                    'href' => '/post/sort-controls',
                    'image' => '/images/images.jpeg',
                    'title' => 'Sort controls that do not dominate the page',
                    'description' => 'A lightweight control bar pattern for article lists where sorting is useful but not the primary action.',
                    'badges' => ['Mindfulness', 'Staff Pick'],
                    'author' => 'James',
                    'date' => 'Jun 29, 2026',
                ],
            ],
            'pagination' => [
                'prev' => null,
                'next' => "/category/{$slug}?page=2",
                'pages' => [
                    ['label' => '1', 'href' => "/category/{$slug}?page=1", 'active' => true],
                    ['label' => '2', 'href' => "/category/{$slug}?page=2", 'active' => false],
                    ['label' => '3', 'href' => "/category/{$slug}?page=3", 'active' => false],
                    ['label' => '4', 'href' => "/category/{$slug}?page=4", 'active' => false],
                ],
            ],
        ]);
    }
}
