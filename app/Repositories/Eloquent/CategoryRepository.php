<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Category\CategoryFilterDTO;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * Get the model class name.
     */
    public function model(): string
    {
        return Category::class;
    }

    /**
     * Fetch all categories with their children (if onlyRoot is false) and filter by the given DTO.
     *
     * @param  bool  $onlyRoot  If true, only fetch root categories.
     * @param  CategoryFilterDTO  $dto  The filter DTO.
     */
    public function getAll($onlyRoot, CategoryFilterDTO $dto): LengthAwarePaginator
    {
        $query = $this->model->with('childrenRecursive');

        if ($onlyRoot) {
            $query->whereNull('parent_id');
        }

        if (isset($dto->filters['is_active'])) {
            $query->where('is_active', $dto->filters['is_active']);
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * Get active categories that have at least one published post.
     *
     * @return Collection Active categories with at least one published post.
     */
    public function getActiveWithPosts(): Collection
    {
        return $this->query()
            ->select(['id', 'name', 'slug'])
            ->where('is_active', true)
            ->whereHas('posts', function ($query) {
                $query->where('status', PostStatus::Published->value);
            })
            ->withCount(['posts' => function ($query) {
                $query->where('status', PostStatus::Published->value);
            }])
            ->orderBy('name')
            ->get();
    }
}
