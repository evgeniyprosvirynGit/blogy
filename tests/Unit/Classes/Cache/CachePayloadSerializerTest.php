<?php

declare(strict_types=1);

use App\Classes\Cache\CachePayloadSerializer;
use App\Models\Category;

it('round-trips arrays through the cache payload serializer', function (): void {
    $serializer = new CachePayloadSerializer();

    $payload = $serializer->serialize([
        'title' => 'Example',
        'views' => 42,
    ]);

    expect($serializer->deserialize($payload))->toBe([
        'title' => 'Example',
        'views' => 42,
    ]);
});

it('allows deserializing whitelisted model objects', function (): void {
    $serializer = new CachePayloadSerializer();
    $category = new Category([
        'name' => 'Design Systems',
        'slug' => 'design-systems',
    ]);

    $payload = $serializer->serialize($category);
    $restored = $serializer->deserialize($payload);

    expect($restored)->toBeInstanceOf(Category::class)
        ->and($restored->slug)->toBe('design-systems');
});

it('rejects non-whitelisted objects in cached payloads', function (): void {
    $serializer = new CachePayloadSerializer();

    $payload = serialize(new stdClass());

    expect(fn (): mixed => $serializer->deserialize($payload))
        ->toThrow(UnexpectedValueException::class);
});
