<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property UserRepositoryInterface $inner
 */
final class CachedUserRepository extends CachedRepository implements UserRepositoryInterface
{
    /**
     * @param  UserRepositoryInterface  $inner  The inner repository (enforced by type hint and check).
     */
    public function __construct(
        BaseRepositoryInterface $inner,
        RepositoryCache $cache,
        string $namespace
    ) {
        if (! $inner instanceof UserRepositoryInterface) {
            throw new RepositoryException('Inner repository must implement UserRepositoryInterface');
        }
        parent::__construct($inner, $cache, $namespace);
    }

    /**
     * Retrieves the top authors based on the number of posts they have written.
     *
     * @param  int  $limit  The maximum number of authors to return.
     * @return Collection The top authors.
     */
    public function getTopAuthors(int $limit = 4): Collection
    {
        return $this->remember(
            'getTopAuthors',
            [$limit],
            fn () => $this->inner->getTopAuthors($limit)
        );
    }
}
