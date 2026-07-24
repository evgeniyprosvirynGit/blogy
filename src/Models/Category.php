<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\CacheManager;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category');
    }

    /**
     * @param array<string, mixed> $options
     */
    public function save(array $options = []): bool
    {
        $saved = parent::save($options);

        if ($saved) {
            CacheManager::clear();
        }

        return $saved;
    }

    public function delete(): ?bool
    {
        $deleted = parent::delete();

        if ($deleted) {
            CacheManager::clear();
        }

        return $deleted;
    }

    /**
     * @return Collection<int, self>
     */
    public static function forHomepage(int $limit = 4): Collection
    {
        return CacheManager::remember(
            "homepage.categories.{$limit}",
            static fn (): Collection => self::query()
                ->select(['id', 'name', 'slug', 'description'])
                ->orderBy('name')
                ->limit($limit)
                ->get(),
            CacheManager::ttl('homepage_categories', 900),
        );
    }

    public static function findBySlugForPage(string $slug): ?self
    {
        return CacheManager::remember(
            "category.slug.{$slug}",
            static fn (): ?self => self::query()
                ->select(['id', 'name', 'slug', 'description'])
                ->where('slug', $slug)
                ->first(),
            CacheManager::ttl('category', 1800),
        );
    }
}
