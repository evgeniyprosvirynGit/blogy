<?php

declare(strict_types=1);

namespace App\Models;

use App\Classes\Categories\Enums\CategoryPostSortKey;
use App\Core\CacheManager;
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

    /**
     * @param array<string, mixed> $options
     */
    public function save(array $options = []): bool
    {
        $saved = parent::save($options);

        if ($saved) {
            $this->invalidatePostCache();
        }

        return $saved;
    }

    public function delete(): ?bool
    {
        $deleted = parent::delete();

        if ($deleted) {
            $this->invalidatePostCache();
        }

        return $deleted;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function openArticlePageBySlug(string $slug): ?array
    {
        $post = self::query()
            ->select(['id', 'slug'])
            ->where('slug', $slug)
            ->first();

        if ($post === null) {
            return null;
        }

        self::query()->where('id', $post->id)->increment('views');

        self::invalidateViewSensitiveCache($slug);

        return self::articlePageDataBySlug($slug);
    }

    private function invalidatePostCache(): void
    {
        CacheManager::forgetByPrefix('homepage.posts.');
        CacheManager::forgetByPrefix('category.posts.');
        CacheManager::forgetByPrefix('post.slug.');
        CacheManager::forgetByPrefix('post.related.');
    }

    private static function invalidateViewSensitiveCache(string $slug): void
    {
        CacheManager::forgetByPrefix("post.slug.{$slug}");
        CacheManager::forgetByPrefix('category.posts.');
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
     * @return array<string, mixed>|null
     */
    public static function articlePageDataBySlug(string $slug): ?array
    {
        return CacheManager::remember(
            "post.slug.{$slug}",
            static function () use ($slug): ?array {
                $rows = Capsule::table('posts')
                    ->leftJoin('post_category', 'post_category.post_id', '=', 'posts.id')
                    ->leftJoin('categories', 'categories.id', '=', 'post_category.category_id')
                    ->select([
                        'posts.id',
                        'posts.title',
                        'posts.slug',
                        'posts.image',
                        'posts.description',
                        'posts.content',
                        'posts.views',
                        'posts.published_at',
                        'categories.id as category_id',
                        'categories.name as category_name',
                        'categories.slug as category_slug',
                    ])
                    ->where('posts.slug', $slug)
                    ->orderBy('categories.id')
                    ->get();

                if ($rows->isEmpty()) {
                    return null;
                }

                $post = $rows->first();
                $categoryIds = [];
                $primaryCategory = null;

                foreach ($rows as $row) {
                    if ($row->category_id === null) {
                        continue;
                    }

                    $categoryIds[] = (int) $row->category_id;

                    if ($primaryCategory === null) {
                        $primaryCategory = [
                            'name' => (string) $row->category_name,
                            'slug' => (string) $row->category_slug,
                        ];
                    }
                }

                return [
                    'id' => $post->id,
                    'title' => (string) $post->title,
                    'category' => $primaryCategory,
                    'category_ids' => array_values(array_unique($categoryIds)),
                    'image' => (string) $post->image,
                    'description' => (string) $post->description,
                    'publishedAt' => $post->published_at !== null ? date('F j, Y', strtotime((string) $post->published_at)) : '',
                    'views' => number_format((int) $post->views),
                    'paragraphs' => self::contentParagraphs((string) $post->content),
                ];
            },
            CacheManager::ttl('article', 1800),
        );
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

        $cacheKey = sprintf(
            'homepage.posts.%s.%d',
            md5(implode(',', $categoryIds)),
            $perCategory,
        );

        return CacheManager::remember(
            $cacheKey,
            static function () use ($categoryIds, $perCategory): array {
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
            },
            CacheManager::ttl('homepage_posts', 600),
        );
    }

    /**
     * @return array{items: array<int, array<string, string>>, total: int, current_page: int}
     */
    public static function previewCardsForCategory(int $categoryId, string $sort = 'published_at', int $perPage = 12, int $page = 1): array
    {
        $sortKey = CategoryPostSortKey::fromNullable($sort);
        $page = max(1, $page);

        return CacheManager::remember(
            "category.posts.{$categoryId}.{$sortKey->value}.{$perPage}.{$page}",
            static function () use ($categoryId, $sortKey, $perPage, $page): array {
                $baseQuery = self::query()
                    ->select(['posts.slug', 'posts.image', 'posts.title', 'posts.description', 'posts.views', 'posts.published_at'])
                    ->join('post_category', 'post_category.post_id', '=', 'posts.id')
                    ->where('post_category.category_id', $categoryId);

                $total = (clone $baseQuery)->count('posts.id');
                $totalPages = max(1, (int) ceil($total / max(1, $perPage)));
                $currentPage = min($page, $totalPages);

                $items = $baseQuery
                    ->orderBy("posts.{$sortKey->column()}", $sortKey->direction())
                    ->orderByDesc('posts.id')
                    ->forPage($currentPage, $perPage)
                    ->get()
                    ->map(static fn (self $post): array => [
                        'href' => "/post/{$post->slug}",
                        'image' => (string) $post->image,
                        'title' => $post->title,
                        'description' => $post->description,
                        'meta' => trim(sprintf('%s • %s views', $post->published_at?->format('M j, Y') ?? '', number_format((int) $post->views))),
                    ])
                    ->all();

                return [
                    'items' => $items,
                    'total' => $total,
                    'current_page' => $currentPage,
                ];
            },
            CacheManager::ttl('category_posts', 600),
        );
    }

    /**
     * @param array<int, int> $categoryIds
     * @return array<int, array{href: string, image: string, title: string, meta: string, description: string}>
     */
    public static function relatedPreviewCards(array $categoryIds, int $excludePostId, int $limit = 3): array
    {
        $cacheKey = sprintf(
            'post.related.%d.%d.%s',
            $excludePostId,
            $limit,
            md5(implode(',', $categoryIds)),
        );

        return CacheManager::remember(
            $cacheKey,
            static function () use ($categoryIds, $excludePostId, $limit): array {
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
            },
            CacheManager::ttl('related_posts', 600),
        );
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

    /**
     * @return array<int, string>
     */
    private static function contentParagraphs(string $content): array
    {
        $paragraphs = preg_split("/\n\s*\n/", trim($content)) ?: [];

        return array_values(array_filter(array_map('trim', $paragraphs)));
    }
}
