<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Enums\ApplicationError;
use App\Classes\Images\ResponsiveImageService;
use App\Classes\Posts\RelatedArticlesProvider;
use App\Core\Controller;
use App\Core\View;
use App\Models\Post;
use Throwable;

final class PostController extends Controller
{
    public function __construct(
        View $view,
        ErrorHandlerInterface $errorHandler,
        private readonly ResponsiveImageService $responsiveImageService,
        private readonly RelatedArticlesProvider $relatedArticlesProvider,
    ) {
        parent::__construct($view, $errorHandler);
    }

    public function show(string $slug): string
    {
        try {
            $post = Post::articlePageDataBySlug($slug);

            if ($post === null) {
                return $this->handleError(ApplicationError::ARTICLE_NOT_FOUND);
            }

            $relatedPosts = $this->relatedArticlesProvider->forArticle($post);
            $post['image'] = $this->responsiveImageService->make($post['image'], 'article_cover');

            return $this->render('post/show.tpl', [
                'pageTitle' => $post['title'],
                'post' => $post,
                'relatedPosts' => $this->mapRelatedPosts($relatedPosts),
                'relatedPostsCount' => count($relatedPosts),
            ]);
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::ARTICLE_UNAVAILABLE, $exception);
        }
    }

    /**
     * @param array<int, array<string, string>> $relatedPosts
     * @return array<int, array<string, mixed>>
     */
    private function mapRelatedPosts(array $relatedPosts): array
    {
        return array_map(function (array $post): array {
            $post['image'] = $this->responsiveImageService->make($post['image'], 'related_card');

            return $post;
        }, $relatedPosts);
    }
}
