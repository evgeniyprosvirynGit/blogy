<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Support\BlogDemoData;

final class CategoryController extends Controller
{
    public function show(string $slug): string
    {
        $category = $this->findCategory($slug);
        $posts = $this->postsForCategory($category['id']);

        return $this->render('category/show.tpl', [
            'pageTitle' => $category['name'],
            'category' => [
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
                'articleCount' => count($posts),
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
            'posts' => $posts,
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

    /**
     * @return array<string, mixed>
     */
    private function findCategory(string $slug): array
    {
        foreach (BlogDemoData::categories() as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }

        return [
            'id' => 0,
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'description' => 'Curated articles, practical notes, and editor picks collected in one category feed layout.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function postsForCategory(int $categoryId): array
    {
        $postIds = [];

        foreach (BlogDemoData::postCategories() as $relation) {
            if ($relation['category_id'] === $categoryId) {
                $postIds[] = $relation['post_id'];
            }
        }

        $posts = [];

        foreach (BlogDemoData::posts() as $post) {
            if (! in_array($post['id'], $postIds, true)) {
                continue;
            }

            $posts[] = [
                'published_at' => $post['published_at'],
                'href' => "/post/{$post['slug']}",
                'image' => $post['image'],
                'title' => $post['title'],
                'description' => $post['description'],
                'badges' => ['Editorial', 'Staff Pick'],
                'author' => 'James',
                'date' => date('M j, Y', strtotime($post['published_at'])),
            ];
        }

        usort(
            $posts,
            static fn (array $left, array $right): int => strcmp($right['published_at'], $left['published_at']),
        );

        return array_map(static function (array $post): array {
            unset($post['published_at']);

            return $post;
        }, $posts);
    }
}
