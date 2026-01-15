<?php

namespace App\Repositories\Cache;

use App\Repositories\Contracts\SoftDeletesRepository;
use App\Repositories\Exceptions\RepositoryException;

class SoftDeleteCachedRepository extends CachedRepository implements SoftDeletesRepository
{
    public function restoreMany(array $ids): int
    {
        if (! $this->inner instanceof SoftDeletesRepository) {
            throw new RepositoryException('Inner repository does not support soft deletes.');
        }

        return $this->inner->restoreMany($ids);
    }

    public function forceDeleteMany(array $ids): int
    {
        if (! $this->inner instanceof SoftDeletesRepository) {
            throw new RepositoryException('Inner repository does not support soft deletes.');
        }

        return $this->inner->forceDeleteMany($ids);
    }
}
