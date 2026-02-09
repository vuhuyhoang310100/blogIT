<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Repositories\Contracts\PermissionRepositoryInterface;

final class CachedPermissionRepository extends CachedRepository implements PermissionRepositoryInterface {}
