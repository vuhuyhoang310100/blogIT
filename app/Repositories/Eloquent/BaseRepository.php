<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Throwable;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $class = $this->model();
        if (! is_subclass_of($class, Model::class)) {
            throw new RepositoryException("Invalid model for repository: {$class}");
        }
        $this->model = new $class;
    }

    /** @return class-string<Model> */
    abstract public function model(): string;

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->query()->select($columns)->find($id);
    }

    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->query()->select($columns)->findOrFail($id);
    }

    public function getByIds(array $ids, array $columns = ['*']): Collection
    {
        $ids = array_values(array_unique(array_filter($ids, fn ($v) => $v !== null && $v !== '')));
        if ($ids === []) {
            return $this->model->newCollection();
        }

        return $this->query()->select($columns)->whereKey($ids)->get();
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query()->select($columns)->paginate($perPage);
    }

    public function create(array $attributes): Model
    {
        try {
            return $this->query()->create($attributes);
        } catch (Throwable $e) {
            throw new RepositoryException("Create failed: {$this->model()}", 0, $e);
        }
    }

    public function update(Model $model, array $attributes): Model
    {
        try {
            $model->fill($attributes);
            $model->save();

            return $model;
        } catch (Throwable $e) {
            throw new RepositoryException("Update failed: {$this->model()}", 0, $e);
        }
    }

    public function delete(Model $model): bool
    {
        try {
            return (bool) $model->delete();
        } catch (Throwable $e) {
            throw new RepositoryException("Delete failed: {$this->model()}", 0, $e);
        }
    }

    public function deleteMany(array $ids): int
    {
        $ids = array_values(array_unique(array_filter($ids, fn ($v) => $v !== null && $v !== '')));
        if ($ids === []) {
            return 0;
        }

        try {
            return (int) $this->query()->whereKey($ids)->delete();
        } catch (Throwable $e) {
            throw new RepositoryException("DeleteMany failed: {$this->model()}", 0, $e);
        }
    }
}
