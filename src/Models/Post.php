<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'content',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    public static function findBySlugForArticlePage(string $slug): ?self
    {
        return self::query()
            ->select(['id', 'title', 'slug', 'image', 'description', 'content', 'views', 'published_at'])
            ->with(['categories' => static fn ($query) => $query->select(['categories.id', 'categories.name', 'categories.slug'])])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @param array<int, int> $categoryIds
     * @return array<int, array<int, array<string, string>>>
     */
    public static function groupedPreviewCardsForCategoryIds(array $categoryIds, int $perCategory = 3): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $rankedPosts = Capsule::table('post_category')
            ->join('posts', 'posts.id', '=', 'post_category.post_id')
            ->whereIn('post_category.category_id', $categoryIds)
            ->selectRaw(
                'post_category.category_id, posts.slug, posts.image, posts.title, posts.description, posts.published_at, ' .
                'ROW_NUMBER() OVER (PARTITION BY post_category.category_id ORDER BY posts.published_at DESC, posts.id DESC) as row_num'
            );

        $rows = self::query()
            ->fromSub($rankedPosts, 'ranked_posts')
            ->select([
                'ranked_posts.category_id',
                'ranked_posts.slug',
                'ranked_posts.image',
                'ranked_posts.title',
                'ranked_posts.description',
                'ranked_posts.published_at',
            ])
            ->where('ranked_posts.row_num', '<=', $perCategory)
            ->orderBy('ranked_posts.category_id')
            ->orderByDesc('ranked_posts.published_at')
            ->get();

        return self::mapPreviewCardsByCategory($rows);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function previewCardsForCategory(int $categoryId, string $sort = 'published_at', int $perPage = 12): array
    {
        $orderColumn = $sort === 'views' ? 'views' : 'published_at';

        return self::query()
            ->select(['posts.slug', 'posts.image', 'posts.title', 'posts.description', 'posts.views', 'posts.published_at'])
            ->join('post_category', 'post_category.post_id', '=', 'posts.id')
            ->where('post_category.category_id', $categoryId)
            ->orderByDesc("posts.{$orderColumn}")
            ->orderByDesc('posts.id')
            ->limit($perPage)
            ->get()
            ->map(static fn (self $post): array => [
                'href' => "/post/{$post->slug}",
                'image' => (string) $post->image,
                'title' => $post->title,
                'description' => $post->description,
                'meta' => trim(sprintf('%s • %s views', $post->published_at?->format('M j, Y') ?? '', number_format((int) $post->views))),
            ])
            ->all();
    }

    /**
     * @param array<int, int> $categoryIds
     * @return array<int, array{href: string, image: string, title: string, meta: string, description: string}>
     */
    public static function relatedPreviewCards(array $categoryIds, int $excludePostId, int $limit = 3): array
    {
        $query = self::query()
            ->select(['posts.id', 'posts.slug', 'posts.image', 'posts.title', 'posts.description', 'posts.published_at'])
            ->where('posts.id', '!=', $excludePostId)
            ->orderByDesc('posts.published_at')
            ->orderByDesc('posts.id')
            ->limit($limit);

        if ($categoryIds !== []) {
            $query->join('post_category', 'post_category.post_id', '=', 'posts.id')
                ->whereIn('post_category.category_id', $categoryIds)
                ->distinct();
        }

        return $query->get()
            ->map(static fn (self $post): array => [
                'href' => "/post/{$post->slug}",
                'image' => (string) $post->image,
                'title' => $post->title,
                'meta' => 'Related article',
                'description' => $post->description,
            ])
            ->all();
    }

    /**
     * @param Collection<int, self|object> $rows
     * @return array<int, array<int, array<string, string>>>
     */
    private static function mapPreviewCardsByCategory(Collection $rows): array
    {
        $postsByCategory = [];

        foreach ($rows as $row) {
            $postsByCategory[(int) $row->category_id][] = [
                'slug' => (string) $row->slug,
                'image' => (string) $row->image,
                'title' => (string) $row->title,
                'description' => (string) $row->description,
                'published_label' => date('M j, Y', strtotime((string) $row->published_at)),
            ];
        }

        return $postsByCategory;
    }
}
