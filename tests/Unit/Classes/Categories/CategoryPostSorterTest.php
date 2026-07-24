<?php

declare(strict_types=1);

use App\Classes\Categories\Enums\CategoryPostSortKey;
use App\Classes\Categories\CategoryPostSorter;

it('resolves current category sort', function (): void {
    $sorter = new CategoryPostSorter();

    expect($sorter->current(CategoryPostSortKey::VIEWS->value))->toBe(CategoryPostSortKey::VIEWS->value)
        ->and($sorter->current(CategoryPostSortKey::PUBLICATION_DATE->value))->toBe(CategoryPostSortKey::PUBLICATION_DATE->value)
        ->and($sorter->current('anything-else'))->toBe(CategoryPostSortKey::PUBLICATION_DATE->value)
        ->and($sorter->current(null))->toBe(CategoryPostSortKey::PUBLICATION_DATE->value);
});

it('builds category sort options', function (): void {
    $sorter = new CategoryPostSorter();

    expect($sorter->options('design-systems'))->toBe([
        [
            'label' => 'By publication date',
            'value' => CategoryPostSortKey::PUBLICATION_DATE->value,
            'href' => '/category/design-systems?sort=' . CategoryPostSortKey::PUBLICATION_DATE->value,
        ],
        [
            'label' => 'By views',
            'value' => CategoryPostSortKey::VIEWS->value,
            'href' => '/category/design-systems?sort=' . CategoryPostSortKey::VIEWS->value,
        ],
    ]);
});
