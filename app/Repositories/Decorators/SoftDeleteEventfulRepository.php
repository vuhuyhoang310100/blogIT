<?php

namespace App\Repositories\Decorators;

use App\Repositories\Contracts\SoftDeletesRepository;
use App\Repositories\Events\RepositoryChanged;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Support\Facades\Event;

class SoftDeleteEventfulRepository extends EventfulRepository implements SoftDeletesRepository
{
    public function restoreMany(array $ids): int
    {
        if (! $this->inner instanceof SoftDeletesRepository) {
            throw new RepositoryException('Inner repository does not support soft deletes.');
        }

        $affected = $this->inner->restoreMany($ids);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $affected;
    }

    public function forceDeleteMany(array $ids): int
    {
        if (! $this->inner instanceof SoftDeletesRepository) {
            throw new RepositoryException('Inner repository does not support soft deletes.');
        }

        $affected = $this->inner->forceDeleteMany($ids);
        Event::dispatch(new RepositoryChanged($this->namespace));

        return $affected;
    }
}
