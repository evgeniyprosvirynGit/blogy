<?php

declare(strict_types=1);

namespace App\Models;

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
}
