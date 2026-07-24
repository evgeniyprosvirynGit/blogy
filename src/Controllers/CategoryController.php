<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Images\ResponsiveImageService;
use App\Classes\Pagination\Contracts\PaginatorInterface;
use App\Classes\Sorting\Contracts\SorterInterface;
use App\Core\Controller;
use App\Core\View;
use App\Models\Category;
use App\Models\Post;
use Throwable;

final class CategoryController extends Controller
{
    public function __construct(
        View $view,
        ErrorHandlerInterface $errorHandler,
        private readonly ResponsiveImageService $responsiveImageService,
        private readonly int $postsPerPage,
        private readonly SorterInterface $categoryPostSorter,
        private readonly PaginatorInterface $categoryPaginator,
    ) {
        parent::__construct($view, $errorHandler);
    }

    public function show(string $slug): string
    {
        try {
            $category = $this->categoryFromDatabase($slug);

            if ($category === null) {
                return $this->handleError(ApplicationError::CATEGORY_NOT_FOUND);
            }

            $sort = $this->currentSort();
            $posts = $this->categoryPostsFromDatabase($category->id, $sort);

            return $this->render('category/show.tpl', [
                'pageTitle' => $category->name,
                'category' => $this->mapCategory($category, $posts),
                'sortOptions' => $this->categoryPostSorter->options($slug),
                'currentSort' => $sort,
                'posts' => $posts,
                'pagination' => $this->categoryPaginator->build($slug),
            ]);
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::CATEGORY_UNAVAILABLE, $exception);
        }
    }

    private function currentSort(): string
    {
        return $this->categoryPostSorter->current($_GET['sort'] ?? null);
    }

    private function categoryFromDatabase(string $slug): ?Category
    {
        return Category::findBySlugForPage($slug);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function categoryPostsFromDatabase(int $categoryId, string $sort): array
    {
        return array_map(function (array $post): array {
            $post['image'] = $this->responsiveImageService->make($post['image'], 'category_card');

            return $post;
        }, Post::previewCardsForCategory($categoryId, $sort, $this->postsPerPage));
    }

    /**
     * @param array<int, array<string, string>> $posts
     * @return array<string, mixed>
     */
    private function mapCategory(Category $category, array $posts): array
    {
        return [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'articleCount' => count($posts),
        ];
    }

}
