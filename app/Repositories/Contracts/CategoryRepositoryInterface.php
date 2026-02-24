<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Category\CategoryFilterDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Fetch all categories with their children (if onlyRoot is false) and filter by the given DTO.
     *
     * @param  bool  $onlyRoot  If true, only fetch root categories.
     * @param  CategoryFilterDTO  $dto  The filter DTO.
     */
    public function getAll($onlyRoot, CategoryFilterDTO $dto): LengthAwarePaginator;

    /**
     * Get active categories that have at least one published post.
     */
    public function getActiveWithPosts(): Collection;
}
