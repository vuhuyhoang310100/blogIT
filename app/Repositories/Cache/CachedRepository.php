<?php

namespace App\Repositories\Cache;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CachedRepository implements BaseRepositoryInterface
{
    public function __construct(
        protected readonly BaseRepositoryInterface $inner,
        protected readonly RepositoryCache $cache,
        protected readonly string $namespace
    ) {}

    /** @return class-string<Model> */
    public function model(): string
    {
        return $this->inner->model();
    }

    public function query(): Builder
    {
        return $this->inner->query();
    }

    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        /** @var ?Model */
        return $this->remember(
            'find',
            [$id, $columns],
            fn (): ?Model => $this->inner->find($id, $columns)
        );
    }

    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->inner->findOrFail($id, $columns);
    }

    public function getByIds(array $ids, array $columns = ['*']): Collection
    {
        /** @var Collection */
        return $this->remember(
            'getByIds',
            [$ids, $columns],
            fn (): Collection => $this->inner->getByIds($ids, $columns)
        );
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $page = (int) request()->integer('page', 1);

        /** @var LengthAwarePaginator */
        return $this->remember(
            'paginate',
            [$perPage, $columns, $page],
            fn (): LengthAwarePaginator => $this->inner->paginate($perPage, $columns)
        );
    }

    // Writes: không cache, đi thẳng
    public function create(array $attributes): Model
    {
        return $this->inner->create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        return $this->inner->update($model, $attributes);
    }

    public function delete(Model $model): bool
    {
        return $this->inner->delete($model);
    }

    public function deleteMany(array $ids): int
    {
        return $this->inner->deleteMany($ids);
    }

    /**
     * @template T
     *
     * @param  \Closure():T  $callback
     * @return T
     */
    private function remember(string $method, array $parts, \Closure $callback): mixed
    {
        if (! $this->cache->enabled()) {
            return $callback();
        }

        $store = $this->cache->store();
        $keys = new CacheKeys($this->cache->prefix(), $this->namespace);

        $v = (int) $store->get($keys->versionKey(), 1);
        $key = $keys->make($method, $parts).':v'.$v;

        if ($this->cache->useTags() && $this->cache->supportsTags()) {
            return $store->tags([$keys->tag()])->remember($key, $this->cache->ttl(), $callback);
        }

        return $store->remember($key, $this->cache->ttl(), $callback);
    }
}
