<?php

declare(strict_types=1);

namespace App\Repositories\Decorators;

use App\Repositories\Contracts\PermissionRepositoryInterface;

final class EventfulPermissionRepository extends EventfulRepository implements PermissionRepositoryInterface {}
