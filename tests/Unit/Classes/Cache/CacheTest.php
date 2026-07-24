<?php

declare(strict_types=1);

use App\Classes\Cache\Cache;
use App\Classes\Cache\Contracts\CacheStoreInterface;

it('returns default when cache key is missing', function (): void {
    $cache = new Cache(new InMemoryCacheStore());

    expect($cache->get('missing', 'fallback'))->toBe('fallback');
});

it('stores and retrieves cached values', function (): void {
    $cache = new Cache(new InMemoryCacheStore());

    $cache->put('post:1', ['title' => 'Test']);

    expect($cache->get('post:1'))->toBe(['title' => 'Test'])
        ->and($cache->has('post:1'))->toBeTrue();
});

it('remembers values only once per key', function (): void {
    $cache = new Cache(new InMemoryCacheStore());
    $calls = 0;

    $first = $cache->remember('homepage', function () use (&$calls): array {
        $calls++;

        return ['cached' => true];
    });

    $second = $cache->remember('homepage', function () use (&$calls): array {
        $calls++;

        return ['cached' => false];
    });

    expect($first)->toBe(['cached' => true])
        ->and($second)->toBe(['cached' => true])
        ->and($calls)->toBe(1);
});

it('forgets keys and clears the store', function (): void {
    $cache = new Cache(new InMemoryCacheStore());

    $cache->put('one', 1);
    $cache->put('two', 2);

    expect($cache->forget('one'))->toBeTrue()
        ->and($cache->has('one'))->toBeFalse()
        ->and($cache->clear())->toBeTrue()
        ->and($cache->has('two'))->toBeFalse();
});

it('forgets keys by prefix without clearing unrelated entries', function (): void {
    $cache = new Cache(new InMemoryCacheStore());

    $cache->put('category.posts.1.published_at.12.1', ['cached' => true]);
    $cache->put('category.posts.1.views.12.1', ['cached' => true]);
    $cache->put('custom.key', 'keep');

    expect($cache->forgetByPrefix('category.posts.1.'))->toBeTrue()
        ->and($cache->has('category.posts.1.published_at.12.1'))->toBeFalse()
        ->and($cache->has('category.posts.1.views.12.1'))->toBeFalse()
        ->and($cache->get('custom.key'))->toBe('keep');
});

final class InMemoryCacheStore implements CacheStoreInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $items = [];

    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->items[$key] = $value;

        return true;
    }

    public function delete(string $key): bool
    {
        $exists = array_key_exists($key, $this->items);
        unset($this->items[$key]);

        return $exists;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function deleteByPrefix(string $prefix): bool
    {
        $deleted = false;

        foreach (array_keys($this->items) as $key) {
            if (! str_starts_with($key, $prefix)) {
                continue;
            }

            unset($this->items[$key]);
            $deleted = true;
        }

        return $deleted;
    }

    public function clear(): bool
    {
        $this->items = [];

        return true;
    }
}
