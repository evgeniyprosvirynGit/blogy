<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Errors\Enums\ApplicationError;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Post;
use Throwable;

final class CategoryController extends Controller
{
    public function show(string $slug): string
    {
        try {
            $category = Category::findBySlugForPage($slug);

            if ($category === null) {
                return $this->handleError(ApplicationError::CATEGORY_NOT_FOUND);
            }

            $sort = $this->currentSort();
            $posts = Post::previewCardsForCategory($category->id, $sort);

            return $this->render('category/show.tpl', [
                'pageTitle' => $category->name,
                'category' => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
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
                'currentSort' => $sort,
                'posts' => $posts,
                'pagination' => [
                    'prev' => null,
                    'next' => null,
                    'pages' => [
                        ['label' => '1', 'href' => "/category/{$slug}?page=1", 'active' => true],
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::CATEGORY_UNAVAILABLE, $exception);
        }
    }

    private function currentSort(): string
    {
        $sort = $_GET['sort'] ?? 'published_at';

        return $sort === 'views' ? 'views' : 'published_at';
    }
}
