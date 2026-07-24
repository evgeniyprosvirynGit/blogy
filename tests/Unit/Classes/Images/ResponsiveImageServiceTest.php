<?php

declare(strict_types=1);

use App\Classes\Images\ResponsiveImageService;

it('builds cached responsive variants for a preset', function (): void {
    $service = new ResponsiveImageService(testImagesConfig());

    $image = $service->make('/images/blog.jpg', 'home_card');

    expect($image['src'])->toContain('/images/cache/home_card/')
        ->and($image['srcset'])->toContain('.jpg 384w')
        ->and($image['webp_srcset'])->toContain('.webp 384w')
        ->and($image['sizes'])->toBe('(min-width: 768px) 384px, 100vw');
});

it('falls back to original image when source is missing', function (): void {
    $service = new ResponsiveImageService(testImagesConfig());

    $image = $service->make('/images/missing.jpg', 'home_card');

    expect($image['src'])->toBe('/images/missing.jpg')
        ->and($image['srcset'])->toBe('')
        ->and($image['webp_srcset'])->toBe('');
});
