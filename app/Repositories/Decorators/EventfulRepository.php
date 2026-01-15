<?php

namespace App\Repositories\Decorators;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Events\RepositoryChanged;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class EventfulRepository implements BaseRepositoryInterface
{
    public function __construct(
        protected readonly BaseRepositoryInterface $inner,
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
        return $this->inner->find($id, $columns);
    }

    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->inner->findOrFail($id, $columns);
    }

    public function getByIds(array $ids, array $columns = ['*']): Collection
    {
        return $this->inner->getByIds($ids, $columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->inner->paginate($perPage, $columns);
    }

    public function create(array $attributes): Model
    {
        $model = $this->inner->create($attributes);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $model;
    }

    public function update(Model $model, array $attributes): Model
    {
        $model = $this->inner->update($model, $attributes);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $model;
    }

    public function delete(Model $model): bool
    {
        $ok = $this->inner->delete($model);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $ok;
    }

    public function deleteMany(array $ids): int
    {
        $affected = $this->inner->deleteMany($ids);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $affected;
    }
}
