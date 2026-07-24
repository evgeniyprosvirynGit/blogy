<?php

declare(strict_types=1);

use App\Classes\Categories\CategoryPaginator;

it('builds category pagination payload', function (): void {
    $paginator = new CategoryPaginator();

    expect($paginator->build('design-systems'))->toBe([
        'prev' => null,
        'next' => null,
        'pages' => [
            ['label' => '1', 'href' => '/category/design-systems?page=1', 'active' => true],
        ],
    ]);
});
