<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\DTOs\Category\CategoryFilterDTO;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property CategoryRepositoryInterface $inner
 */
final class CachedCategoryRepository extends CachedRepository implements CategoryRepositoryInterface
{
    /**
     * @param  CategoryRepositoryInterface  $inner  The inner repository (enforced by type hint and check).
     */
    public function __construct(
        BaseRepositoryInterface $inner,
        RepositoryCache $cache,
        string $namespace
    ) {
        if (! $inner instanceof CategoryRepositoryInterface) {
            throw new RepositoryException('Inner repository must implement CategoryRepositoryInterface');
        }

        parent::__construct($inner, $cache, $namespace);
    }

    /**
     * Fetch all categories with their children (if onlyRoot is false) and filter by the given DTO.
     *
     * @param  bool  $onlyRoot  If true, only fetch root categories.
     * @param  CategoryFilterDTO  $dto  The filter DTO.
     */
    public function getAll($onlyRoot, CategoryFilterDTO $dto): LengthAwarePaginator
    {
        return $this->inner->getAll($onlyRoot, $dto);
    }

    /**
     * Get active categories that have at least one published post.
     */
    public function getActiveWithPosts(): Collection
    {
        return $this->remember(
            'getActiveWithPosts',
            [],
            fn (): Collection => $this->inner->getActiveWithPosts()
        );
    }
}
