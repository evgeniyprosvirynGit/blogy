<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Support\BlogDemoData;

final class HomeController extends Controller
{
    public function index(): string
    {
        $categories = [];

        foreach (BlogDemoData::categories() as $category) {
            $category['posts'] = array_values(array_filter(
                $this->demoPosts(),
                static fn (array $post): bool => in_array($category['id'], $post['category_ids'], true),
            ));

            $categories[] = $category;
        }

        return $this->render('home/index.tpl', [
            'pageTitle' => 'Simple PHP Blog',
            'categories' => $categories,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function demoPosts(): array
    {
        $postCategoryMap = [];

        foreach (BlogDemoData::postCategories() as $relation) {
            $postCategoryMap[$relation['post_id']][] = $relation['category_id'];
        }

        return array_map(
            static fn (array $post): array => [
                'slug' => $post['slug'],
                'image' => $post['image'],
                'title' => $post['title'],
                'description' => $post['description'],
                'published_label' => date('M j, Y', strtotime($post['published_at'])),
                'category_ids' => $postCategoryMap[$post['id']] ?? [],
            ],
            BlogDemoData::posts(),
        );
    }
}
