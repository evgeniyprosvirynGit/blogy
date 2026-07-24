<?php

declare(strict_types=1);

use App\Classes\Categories\Enums\CategoryPostSortKey;
use App\Classes\Categories\CategoryPostSorter;

it('resolves current category sort', function (): void {
    $sorter = new CategoryPostSorter();

    expect($sorter->current(CategoryPostSortKey::VIEWS_DESC->value))->toBe(CategoryPostSortKey::VIEWS_DESC->value)
        ->and($sorter->current(CategoryPostSortKey::VIEWS_ASC->value))->toBe(CategoryPostSortKey::VIEWS_ASC->value)
        ->and($sorter->current(CategoryPostSortKey::PUBLICATION_DATE_DESC->value))->toBe(CategoryPostSortKey::PUBLICATION_DATE_DESC->value)
        ->and($sorter->current(CategoryPostSortKey::PUBLICATION_DATE_ASC->value))->toBe(CategoryPostSortKey::PUBLICATION_DATE_ASC->value)
        ->and($sorter->current('views'))->toBe(CategoryPostSortKey::VIEWS_DESC->value)
        ->and($sorter->current('published_at'))->toBe(CategoryPostSortKey::PUBLICATION_DATE_DESC->value)
        ->and($sorter->current('anything-else'))->toBe(CategoryPostSortKey::PUBLICATION_DATE_DESC->value)
        ->and($sorter->current(null))->toBe(CategoryPostSortKey::PUBLICATION_DATE_DESC->value);
});

it('builds category sort options', function (): void {
    $sorter = new CategoryPostSorter();

    expect($sorter->options('design-systems'))->toBe([
        [
            'label' => 'Newest first',
            'value' => CategoryPostSortKey::PUBLICATION_DATE_DESC->value,
            'href' => '/category/design-systems?sort=' . CategoryPostSortKey::PUBLICATION_DATE_DESC->value,
        ],
        [
            'label' => 'Oldest first',
            'value' => CategoryPostSortKey::PUBLICATION_DATE_ASC->value,
            'href' => '/category/design-systems?sort=' . CategoryPostSortKey::PUBLICATION_DATE_ASC->value,
        ],
        [
            'label' => 'Most viewed',
            'value' => CategoryPostSortKey::VIEWS_DESC->value,
            'href' => '/category/design-systems?sort=' . CategoryPostSortKey::VIEWS_DESC->value,
        ],
        [
            'label' => 'Least viewed',
            'value' => CategoryPostSortKey::VIEWS_ASC->value,
            'href' => '/category/design-systems?sort=' . CategoryPostSortKey::VIEWS_ASC->value,
        ],
    ]);
});
