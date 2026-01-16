<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Repositories\Contracts\PostRepositoryInterface;

final class CachedPostRepository extends SoftDeleteCachedRepository implements PostRepositoryInterface {}
