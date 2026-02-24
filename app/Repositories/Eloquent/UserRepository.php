<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Retrieves the class name of the model.
     *
     * @return string The class name of the model.
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Retrieves the top authors based on the number of posts they have written.
     *
     * @param  int  $limit  The maximum number of authors to return.
     * @return Collection The top authors.
     */
    public function getTopAuthors(int $limit = 4): Collection
    {
        return $this->query()
            ->select(['id', 'name'])
            ->with(['roles:id,name'])
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get();
    }
}
