<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Errors\Contracts\ErrorHandlerInterface;
use App\Classes\Errors\Enums\ApplicationError;
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

            return $this->render('post/show.tpl', [
                'pageTitle' => $post['title'],
                'post' => $post,
                'relatedPosts' => $relatedPosts,
                'relatedPostsCount' => count($relatedPosts),
            ]);
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::ARTICLE_UNAVAILABLE, $exception);
        }
    }
}
