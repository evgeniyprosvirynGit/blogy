<?php

declare(strict_types=1);

namespace App\Classes\Pagination\Contracts;

interface PaginatorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function build(string $slug): array;
}
