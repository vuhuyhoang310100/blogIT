<?php

declare(strict_types=1);

namespace App\Services\Traits;

trait SoftDeleteServiceTrait
{
    /**
     * Restore a soft-deleted model by ID.
     */
    public function restore(int|string $id): bool
    {
        /** @var \App\Repositories\Interfaces\SoftDeleteRepositoryInterface $repository */
        $repository = $this->repository;

        return $repository->restore($id);
    }

    /**
     * Permanently remove a model by ID.
     */
    public function forceDelete(int|string $id): bool
    {
        /** @var \App\Repositories\Interfaces\SoftDeleteRepositoryInterface $repository */
        $repository = $this->repository;

        return $repository->forceDelete($id);
    }

    /**
     * Restore multiple soft-deleted models by IDs.
     *
     * @param  array<int|string>  $ids
     */
    public function bulkRestore(array $ids): bool
    {
        /** @var \App\Repositories\Interfaces\SoftDeleteRepositoryInterface $repository */
        $repository = $this->repository;

        return $repository->bulkRestore($ids);
    }

    /**
     * Permanently remove multiple models by IDs.
     *
     * @param  array<int|string>  $ids
     */
    public function bulkForceDelete(array $ids): bool
    {
        /** @var \App\Repositories\Interfaces\SoftDeleteRepositoryInterface $repository */
        $repository = $this->repository;

        return $repository->bulkForceDelete($ids);
    }
}
