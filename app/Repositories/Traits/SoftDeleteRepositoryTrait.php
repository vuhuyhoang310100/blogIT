<?php

declare(strict_types=1);

namespace App\Repositories\Traits;

trait SoftDeleteRepositoryTrait
{
    /**
     * {@inheritDoc}
     */
    public function restore(int|string $id): bool
    {
        /** @var \Illuminate\Database\Eloquent\SoftDeletes $model */
        $model = $this->model->withTrashed()->findOrFail($id);

        return $model->restore();
    }

    /**
     * {@inheritDoc}
     */
    public function forceDelete(int|string $id): bool
    {
        /** @var \Illuminate\Database\Eloquent\SoftDeletes $model */
        $model = $this->model->withTrashed()->findOrFail($id);

        return $model->forceDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function bulkRestore(array $ids): bool
    {
        return $this->model->withTrashed()
            ->whereIn($this->model->getKeyName(), $ids)
            ->restore() > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkForceDelete(array $ids): bool
    {
        return $this->model->withTrashed()
            ->whereIn($this->model->getKeyName(), $ids)
            ->forceDelete() > 0;
    }
}
