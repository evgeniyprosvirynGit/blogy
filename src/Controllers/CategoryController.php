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
            $currentPage = $this->currentPage();
            $postPage = $this->categoryPostsFromDatabase($category->id, $sort, $currentPage);

            return $this->render('category/show.tpl', [
                'pageTitle' => $category->name,
                'category' => $this->mapCategory($category, $postPage['total']),
                'sortOptions' => $this->categoryPostSorter->options($slug),
                'currentSort' => $sort,
                'posts' => $postPage['items'],
                'pagination' => $this->categoryPaginator->build(
                    $slug,
                    $postPage['current_page'] ?? $currentPage,
                    $postPage['total'],
                    $this->postsPerPage,
                    ['sort' => $sort],
                ),
            ]);
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::CATEGORY_UNAVAILABLE, $exception);
        }
    }

    private function currentSort(): string
    {
        return $this->categoryPostSorter->current($_GET['sort'] ?? null);
    }

    private function currentPage(): int
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);

        return $page !== false && $page > 0 ? $page : 1;
    }

    private function categoryFromDatabase(string $slug): ?Category
    {
        return Category::findBySlugForPage($slug);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    private function categoryPostsFromDatabase(int $categoryId, string $sort, int $page): array
    {
        $postPage = Post::previewCardsForCategory($categoryId, $sort, $this->postsPerPage, $page);
        $postPage['items'] = array_map(function (array $post): array {
            $post['image'] = $this->responsiveImageService->make($post['image'], 'category_card');

            return $post;
        }, $postPage['items']);

        return $postPage;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapCategory(Category $category, int $totalArticles): array
    {
        return [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'articleCount' => $totalArticles,
        ];
    }

}
