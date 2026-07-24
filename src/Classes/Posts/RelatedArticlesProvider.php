<?php

declare(strict_types=1);

namespace App\Classes\Posts;

use App\Models\Post;

final readonly class RelatedArticlesProvider
{
    public function __construct(
        private int $limit,
    ) {
    }

    /**
     * @param array<string, mixed> $article
     * @return array<int, array<string, string>>
     */
    public function forArticle(array $article): array
    {
        return Post::relatedPreviewCards(
            $article['category_ids'],
            $article['id'],
            $this->limit,
        );
    }
}
