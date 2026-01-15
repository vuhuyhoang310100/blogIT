<?php

namespace App\Repositories\Cache;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;

final class RepositoryCache
{
    public function enabled(): bool
    {
        return (bool) config('repository.cache.enabled', true);
    }

    public function ttl(): int
    {
        return (int) config('repository.cache.ttl', 300);
    }

    public function prefix(): string
    {
        return (string) config('repository.cache.prefix', 'repo');
    }

    public function useTags(): bool
    {
        return (bool) config('repository.cache.use_tags', true);
    }

    public function store(): CacheRepository
    {
        return Cache::store(config('cache.default'));
    }

    public function supportsTags(): bool
    {
        return method_exists($this->store()->getStore(), 'tags');
    }
}
