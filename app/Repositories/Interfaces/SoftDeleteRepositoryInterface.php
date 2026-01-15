<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface SoftDeleteRepositoryInterface
{
    /**
     * Restore a soft-deleted model by ID.
     */
    public function restore(int|string $id): bool;

    /**
     * Permanently remove a model by ID.
     */
    public function forceDelete(int|string $id): bool;

    /**
     * Restore multiple soft-deleted models by IDs.
     *
     * @param  array<int|string>  $ids
     */
    public function bulkRestore(array $ids): bool;

    /**
     * Permanently remove multiple models by IDs.
     *
     * @param  array<int|string>  $ids
     */
    public function bulkForceDelete(array $ids): bool;
}
