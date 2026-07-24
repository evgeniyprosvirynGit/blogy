<?php

declare(strict_types=1);

namespace App\Classes\Categories\Enums;

enum CategoryPostSortKey: string
{
    case PUBLICATION_DATE_DESC = 'published_at_desc';
    case PUBLICATION_DATE_ASC = 'published_at_asc';
    case VIEWS_DESC = 'views_desc';
    case VIEWS_ASC = 'views_asc';

    public static function fromNullable(?string $value): self
    {
        return match ($value) {
            self::PUBLICATION_DATE_ASC->value => self::PUBLICATION_DATE_ASC,
            self::VIEWS_DESC->value, 'views' => self::VIEWS_DESC,
            self::VIEWS_ASC->value => self::VIEWS_ASC,
            self::PUBLICATION_DATE_DESC->value, 'published_at', null => self::PUBLICATION_DATE_DESC,
            default => self::PUBLICATION_DATE_DESC,
        };
    }

    public function column(): string
    {
        return match ($this) {
            self::PUBLICATION_DATE_DESC, self::PUBLICATION_DATE_ASC => 'published_at',
            self::VIEWS_DESC, self::VIEWS_ASC => 'views',
        };
    }

    public function direction(): string
    {
        return match ($this) {
            self::PUBLICATION_DATE_DESC, self::VIEWS_DESC => 'desc',
            self::PUBLICATION_DATE_ASC, self::VIEWS_ASC => 'asc',
        };
    }
}
