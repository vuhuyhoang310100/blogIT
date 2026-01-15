<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all models.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     * @return Collection<int, Model>
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get paginated models.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Find a model by ID.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find a model by ID or throw an exception.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Find a model by specific attribute.
     *
     * @param  array<string>  $columns
     * @param  array<string>  $relations
     */
    public function findBy(string $attribute, mixed $value, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Create a new model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(int|string $id, array $attributes): bool;

    /**
     * Delete a model by ID.
     */
    public function delete(int|string $id): bool;

    /**
     * Delete multiple models by IDs.
     *
     * @param  array<int|string>  $ids
     * @return int The number of deleted records.
     */
    public function bulkDelete(array $ids): int;
}
