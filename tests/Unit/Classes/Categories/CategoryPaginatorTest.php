<?php

declare(strict_types=1);

use App\Classes\Categories\CategoryPaginator;

it('builds category pagination payload', function (): void {
    $paginator = new CategoryPaginator();

    expect($paginator->build('design-systems', 2, 25, 12, ['sort' => 'views']))->toBe([
        'prev' => '/category/design-systems?sort=views&page=1',
        'next' => '/category/design-systems?sort=views&page=3',
        'pages' => [
            ['label' => '1', 'href' => '/category/design-systems?sort=views&page=1', 'active' => false],
            ['label' => '2', 'href' => '/category/design-systems?sort=views&page=2', 'active' => true],
            ['label' => '3', 'href' => '/category/design-systems?sort=views&page=3', 'active' => false],
        ],
    ]);
});
