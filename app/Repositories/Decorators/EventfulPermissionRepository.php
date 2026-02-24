<?php

declare(strict_types=1);

namespace App\Repositories\Decorators;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;

/**
 * @property PermissionRepositoryInterface $inner
 */
final class EventfulPermissionRepository extends EventfulRepository implements PermissionRepositoryInterface
{
    /**
     * @param  PermissionRepositoryInterface  $inner  The inner repository (enforced by type hint and check).
     */
    public function __construct(BaseRepositoryInterface $inner, string $namespace)
    {
        if (! $inner instanceof PermissionRepositoryInterface) {
            throw new RepositoryException('Inner repository must implement PermissionRepositoryInterface');
        }

        parent::__construct($inner, $namespace);
    }
}
