<?php

declare(strict_types=1);

namespace App\Classes\Categories\Enums;

enum CategoryPostSortKey: string
{
    case PUBLICATION_DATE = 'published_at';
    case VIEWS = 'views';

    public static function fromNullable(?string $value): self
    {
        return $value === self::VIEWS->value
            ? self::VIEWS
            : self::PUBLICATION_DATE;
    }
}
