<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Images\ResponsiveImageService;
use App\Core\View;
use App\Models\Category;
use App\Models\Post;
use App\Core\Controller;
use Illuminate\Support\Collection;
use Throwable;

final class HomeController extends Controller
{
    public function __construct(
        View $view,
        ErrorHandlerInterface $errorHandler,
        private readonly ResponsiveImageService $responsiveImageService,
        private readonly string $pageTitle,
        private readonly int $categoriesLimit,
        private readonly int $postsPerCategory,
        private readonly string $defaultPostImage,
    ) {
        parent::__construct($view, $errorHandler);
    }

    public function index(): string
    {
        try {
            $categories = $this->homepageCategoriesFromDatabase();
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::HOMEPAGE_CATEGORIES_UNAVAILABLE, $exception);
        }

        if ($categories->isEmpty()) {
            return $this->render('home/index.tpl', $this->emptyHomepagePayload());
        }

        try {
            $postsByCategory = Post::groupedPreviewCardsForCategoryIds(
                $categories->pluck('id')->all(),
                $this->postsPerCategory,
            );
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::HOMEPAGE_POSTS_UNAVAILABLE, $exception);
        }

        return $this->render('home/index.tpl', [
            'pageTitle' => $this->pageTitle,
            'categories' => $this->mapHomepageCategories($categories, $postsByCategory),
            'emptyState' => null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyHomepagePayload(): array
    {
        return [
            'pageTitle' => $this->pageTitle,
            'categories' => [],
            'emptyState' => [
                'title' => 'No categories published yet',
                'message' => 'The homepage is connected to the database, but no categories are available for display yet.',
            ],
        ];
    }

    /**
     * @return Collection<int, Category>
     */
    private function homepageCategoriesFromDatabase(): Collection
    {
        return Category::forHomepage($this->categoriesLimit);
    }

    /**
     * @param array<int, array<int, array<string, string>>> $postsByCategory
     * @return array<int, array<string, mixed>>
     */
    private function mapHomepageCategories(Collection $categories, array $postsByCategory): array
    {
        return $categories
            ->map(function (Category $category) use ($postsByCategory): array {
                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'posts' => $this->mapHomepagePosts($postsByCategory[$category->id] ?? []),
                ];
            })
            ->all();
    }

    /**
     * @param array<int, array<string, string>> $posts
     * @return array<int, array<string, mixed>>
     */
    private function mapHomepagePosts(array $posts): array
    {
        return array_map(function (array $post): array {
            return [
                'href' => "/post/{$post['slug']}",
                'image' => $this->responsiveImageService->make($this->homepagePostImage($post), 'home_card'),
                'title' => $post['title'],
                'meta' => $post['published_label'],
                'description' => $post['description'],
            ];
        }, $posts);
    }

    /**
     * @param array<string, string> $post
     */
    private function homepagePostImage(array $post): string
    {
        return $post['image'] !== '' ? $post['image'] : $this->defaultPostImage;
    }
}
