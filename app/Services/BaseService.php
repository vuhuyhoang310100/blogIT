<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    /**
     * Get all models.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     * @return Collection<int, Model>
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->repository->all($columns, $relations);
    }

    /**
     * Get paginated models.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns, $relations);
    }

    /**
     * Find a model by ID.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->repository->find($id, $columns, $relations);
    }

    /**
     * Find a model by ID or throw an exception.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->repository->findOrFail($id, $columns, $relations);
    }

    /**
     * Find a model by specific attribute.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function findBy(string $attribute, mixed $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->repository->findBy($attribute, $value, $columns, $relations);
    }

    /**
     * Create a new model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    /**
     * Update an existing model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(int|string $id, array $attributes): bool
    {
        return $this->repository->update($id, $attributes);
    }

    /**
     * Delete a model by ID.
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Delete multiple models by IDs.
     *
     * @param  array<int|string>  $ids
     */
    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }
}
