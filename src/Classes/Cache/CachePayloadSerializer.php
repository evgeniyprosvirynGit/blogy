<?php

declare(strict_types=1);

namespace App\Classes\Cache;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use UnexpectedValueException;

final class CachePayloadSerializer
{
    /**
     * @var array<int, class-string>
     */
    private const ALLOWED_CLASSES = [
        Category::class,
        EloquentCollection::class,
        SupportCollection::class,
        Carbon::class,
    ];

    public function serialize(mixed $value): string
    {
        return serialize($value);
    }

    public function deserialize(string $payload): mixed
    {
        $value = unserialize($payload, ['allowed_classes' => self::ALLOWED_CLASSES]);

        if ($value === false && $payload !== serialize(false)) {
            throw new UnexpectedValueException('Unable to deserialize cached payload.');
        }

        if ($value instanceof \__PHP_Incomplete_Class) {
            throw new UnexpectedValueException('Cached payload contains a non-whitelisted object.');
        }

        return $value;
    }
}
