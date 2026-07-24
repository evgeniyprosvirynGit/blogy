<?php

declare(strict_types=1);

namespace App\Classes\Categories;

use App\Classes\Categories\Enums\CategoryPostSortKey;
use App\Classes\Sorting\Contracts\SorterInterface;

final class CategoryPostSorter implements SorterInterface
{
    public function current(?string $sort): string
    {
        return CategoryPostSortKey::fromNullable($sort)->value;
    }

    /**
     * @return array<int, array{label: string, value: string, href: string}>
     */
    public function options(string $slug): array
    {
        return [
            [
                'label' => 'By publication date',
                'value' => CategoryPostSortKey::PUBLICATION_DATE->value,
                'href' => "/category/{$slug}?sort=" . CategoryPostSortKey::PUBLICATION_DATE->value,
            ],
            [
                'label' => 'By views',
                'value' => CategoryPostSortKey::VIEWS->value,
                'href' => "/category/{$slug}?sort=" . CategoryPostSortKey::VIEWS->value,
            ],
        ];
    }
}
