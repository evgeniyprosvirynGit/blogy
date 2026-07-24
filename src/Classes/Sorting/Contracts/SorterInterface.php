<?php

declare(strict_types=1);

namespace App\Classes\Sorting\Contracts;

interface SorterInterface
{
    public function current(?string $sort): string;

    /**
     * @return array<int, array{label: string, value: string, href: string}>
     */
    public function options(string $slug): array;
}
