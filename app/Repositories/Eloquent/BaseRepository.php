<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * Class BaseRepository
 *
 * @template TModel of Model
 *
 * @implements BaseRepositoryInterface<TModel>
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var TModel
     */
    protected Model $model;

    protected ?string $lockMode = null;

    /**
     * BaseRepository constructor.
     *
     * @throws RepositoryException
     */
    public function __construct()
    {
        $class = $this->model();
        if (! is_subclass_of($class, Model::class)) {
            throw new RepositoryException("Invalid model for repository: {$class}");
        }
        $this->model = new $class;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function model(): string;

    /**
     * {@inheritDoc}
     */
    public function lockForUpdate(): static
    {
        $this->lockMode = 'lockForUpdate';

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function sharedLock(): static
    {
        $this->lockMode = 'sharedLock';

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function query(): Builder
    {
        $query = $this->model->newQuery();

        if ($this->lockMode) {
            $query->{$this->lockMode}();
            $this->lockMode = null;
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->query()->with($relations)->select($columns)->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->query()->with($relations)->select($columns)->findOrFail($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(string $attribute, mixed $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->query()->with($relations)->select($columns)->where($attribute, $value)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getByIds(array $ids, array $columns = ['*'], array $relations = []): Collection
    {
        $ids = array_values(array_unique(array_filter($ids, fn ($v) => $v !== null && $v !== '')));
        if ($ids === []) {
            return $this->model->newCollection();
        }

        return $this->query()->with($relations)->select($columns)->whereKey($ids)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $filters = [], array $relations = [], array $orderBy = []): LengthAwarePaginator
    {
        $query = $this->query()->with($relations)->select($columns);

        // Handle Search
        if (isset($filters['q']) && method_exists($this->model, 'scopeSearch')) {
            $query->search($filters['q']);
        }

        $filterData = $filters;
        unset($filterData['q']);

        if (method_exists($this->model, 'scopeFilter')) {
            $query->filter($filterData);
        } else {
            foreach ($filterData as $column => $value) {
                if ($value !== null && $value !== '') {
                    $query->where($column, $value);
                }
            }
        }

        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Model
    {
        try {
            return $this->query()->create($attributes);
        } catch (Throwable $e) {
            throw new RepositoryException("Create failed: {$this->model()}", 0, $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(int|string $id, array $attributes): Model
    {
        try {
            $model = $this->findOrFail($id);
            $model->fill($attributes);
            $model->save();

            return $model;
        } catch (Throwable $e) {
            throw new RepositoryException("Update failed: {$this->model()}", 0, $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int|string $id): bool
    {
        try {
            $model = $this->findOrFail($id);

            return (bool) $model->delete();
        } catch (Throwable $e) {
            throw new RepositoryException("Delete failed: {$this->model()}", 0, $e);
        }
    }

    /**
     * {@inheritDoc}
     */
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
