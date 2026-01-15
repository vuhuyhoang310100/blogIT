<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\SoftDeleteRepositoryInterface;
use App\Repositories\Traits\SoftDeleteRepositoryTrait;

class PostRepository extends BaseRepository implements SoftDeleteRepositoryInterface
{
    use SoftDeleteRepositoryTrait;

    /**
     * PostRepository constructor.
     */
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }
}
