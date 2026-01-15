<?php

namespace App\Repositories\Contracts;

interface SoftDeletesRepository
{
    public function restoreMany(array $ids): int;

    public function forceDeleteMany(array $ids): int;
}
