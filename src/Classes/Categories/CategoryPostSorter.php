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
                'label' => 'Newest first',
                'value' => CategoryPostSortKey::PUBLICATION_DATE_DESC->value,
                'href' => "/category/{$slug}?sort=" . CategoryPostSortKey::PUBLICATION_DATE_DESC->value,
            ],
            [
                'label' => 'Oldest first',
                'value' => CategoryPostSortKey::PUBLICATION_DATE_ASC->value,
                'href' => "/category/{$slug}?sort=" . CategoryPostSortKey::PUBLICATION_DATE_ASC->value,
            ],
            [
                'label' => 'Most viewed',
                'value' => CategoryPostSortKey::VIEWS_DESC->value,
                'href' => "/category/{$slug}?sort=" . CategoryPostSortKey::VIEWS_DESC->value,
            ],
            [
                'label' => 'Least viewed',
                'value' => CategoryPostSortKey::VIEWS_ASC->value,
                'href' => "/category/{$slug}?sort=" . CategoryPostSortKey::VIEWS_ASC->value,
            ],
        ];
    }
}
