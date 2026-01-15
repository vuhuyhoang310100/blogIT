<?php

namespace App\Repositories\Cache;

final class RepositoryCacheInvalidator
{
    public function __construct(private readonly RepositoryCache $cache) {}

    public function invalidate(string $namespace): void
    {
        if (! $this->cache->enabled()) {
            return;
        }

        $store = $this->cache->store();
        $keys = new CacheKeys($this->cache->prefix(), $namespace);

        if ($this->cache->useTags() && $this->cache->supportsTags()) {
            $store->tags([$keys->tag()])->flush();

            return;
        }

        // Fallback cho database/file store: bump version => invalidate O(1)
        $store->forever($keys->versionKey(), (int) $store->get($keys->versionKey(), 1) + 1);
    }
}
