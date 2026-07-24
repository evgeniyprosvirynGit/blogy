<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\Errors\Enums\ApplicationError;
use App\Core\Controller;
use App\Models\Post;
use Throwable;

final class PostController extends Controller
{
    public function show(string $slug): string
    {
        try {
            $post = Post::findBySlugForArticlePage($slug);

            if ($post === null) {
                return $this->handleError(ApplicationError::ARTICLE_NOT_FOUND);
            }

            $relatedPosts = Post::relatedPreviewCards(
                $post->categories->pluck('id')->all(),
                $post->id,
            );

            return $this->render('post/show.tpl', [
                'pageTitle' => $post->title,
                'post' => [
                    'title' => $post->title,
                    'category' => $post->categories->isNotEmpty()
                        ? [
                            'name' => $post->categories->first()->name,
                            'slug' => $post->categories->first()->slug,
                        ]
                        : null,
                    'image' => $post->image,
                    'description' => $post->description,
                    'publishedAt' => $post->published_at?->format('F j, Y') ?? '',
                    'views' => number_format((int) $post->views),
                    'paragraphs' => $this->articleParagraphs((string) $post->content),
                ],
                'relatedPosts' => $relatedPosts,
            ]);
        } catch (Throwable $exception) {
            return $this->handleError(ApplicationError::ARTICLE_UNAVAILABLE, $exception);
        }
    }

    /**
     * @return array<int, string>
     */
    private function articleParagraphs(string $content): array
    {
        $paragraphs = preg_split("/\n\s*\n/", trim($content)) ?: [];

        return array_values(array_filter(array_map('trim', $paragraphs)));
    }
}
