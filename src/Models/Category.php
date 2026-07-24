<?php

declare(strict_types=1);

namespace App\Models;

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
     * @return Collection<int, self>
     */
    public static function forHomepage(int $limit = 4): Collection
    {
        return self::query()
            ->select(['id', 'name', 'slug', 'description'])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public static function findBySlugForPage(string $slug): ?self
    {
        return self::query()
            ->select(['id', 'name', 'slug', 'description'])
            ->where('slug', $slug)
            ->first();
    }
}
