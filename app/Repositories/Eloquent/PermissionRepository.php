<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;

final class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function model(): string
    {
        return Permission::class;
    }
}
