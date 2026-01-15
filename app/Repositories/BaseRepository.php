<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(string $attribute, mixed $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->where($attribute, $value)->first($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int|string $id, array $attributes): bool
    {
        $model = $this->findOrFail($id);

        return $model->update($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int|string $id): bool
    {
        $model = $this->findOrFail($id);

        return $model->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function bulkDelete(array $ids): int
    {
        return $this->model->destroy($ids);
    }
}
