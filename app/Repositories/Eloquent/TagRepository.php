<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\PostStatus;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    /**
     * Get the model class name.
     */
    public function model(): string
    {
        return Tag::class;
    }

    /**
     * Get tags that have at least one published post.
     *
     * @return Collection Tag models with at least one published post.
     */
    public function getActiveWithPosts(): Collection
    {
        return $this->query()
            ->select(['id', 'name', 'slug'])
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
