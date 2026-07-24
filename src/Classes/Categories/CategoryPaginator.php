<?php

declare(strict_types=1);

namespace App\Classes\Categories;

use App\Classes\Pagination\Contracts\PaginatorInterface;

final class CategoryPaginator implements PaginatorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function build(string $slug, int $currentPage, int $totalItems, int $perPage, array $query = []): array
    {
        $totalPages = max(1, (int) ceil($totalItems / max(1, $perPage)));
        $currentPage = max(1, min($currentPage, $totalPages));
        $pages = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $pages[] = [
                'label' => (string) $page,
                'href' => $this->pageHref($slug, $page, $query),
                'active' => $page === $currentPage,
            ];
        }

        return [
            'prev' => $currentPage > 1 ? $this->pageHref($slug, $currentPage - 1, $query) : null,
            'next' => $currentPage < $totalPages ? $this->pageHref($slug, $currentPage + 1, $query) : null,
            'pages' => $pages,
        ];
    }

    private function pageHref(string $slug, int $page, array $query = []): string
    {
        $parameters = array_filter([
            'sort' => $query['sort'] ?? null,
            'page' => $page,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        return "/category/{$slug}?" . http_build_query($parameters);
    }
}
