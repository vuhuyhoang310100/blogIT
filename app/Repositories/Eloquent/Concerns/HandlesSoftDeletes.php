<?php

namespace App\Repositories\Eloquent\Concerns;

use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;

trait HandlesSoftDeletes
{
    protected function ensureSoftDeletes(): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($this->model), true)) {
            throw new RepositoryException('Model does not use SoftDeletes: '.$this->model());
        }
    }

    public function restoreMany(array $ids): int
    {
        $this->ensureSoftDeletes();

        $ids = array_values(array_unique(array_filter($ids, fn ($v) => $v !== null && $v !== '')));
        if ($ids === []) {
            return 0;
        }

        try {
            return (int) $this->query()->withTrashed()->whereKey($ids)->restore();
        } catch (Throwable $e) {
            throw new RepositoryException("RestoreMany failed: {$this->model()}", 0, $e);
        }
    }

    public function forceDeleteMany(array $ids): int
    {
        $this->ensureSoftDeletes();

        $ids = array_values(array_unique(array_filter($ids, fn ($v) => $v !== null && $v !== '')));
        if ($ids === []) {
            return 0;
        }

        try {
            return (int) $this->query()->withTrashed()->whereKey($ids)->forceDelete();
        } catch (Throwable $e) {
            throw new RepositoryException("ForceDeleteMany failed: {$this->model()}", 0, $e);
        }
    }
}
