<?php

declare(strict_types=1);

namespace App\Classes\Categories;

use App\Classes\Pagination\Contracts\PaginatorInterface;

final class CategoryPaginator implements PaginatorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function build(string $slug): array
    {
        return [
            'prev' => null,
            'next' => null,
            'pages' => [
                ['label' => '1', 'href' => "/category/{$slug}?page=1", 'active' => true],
            ],
        ];
    }
}
