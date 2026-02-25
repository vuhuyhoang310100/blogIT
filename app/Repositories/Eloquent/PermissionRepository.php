<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;

final class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    /**
     * Get the model class for the repository.
     */
    public function model(): string
    {
        return Permission::class;
    }
}
