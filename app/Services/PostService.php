<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PostRepository;
use App\Services\Traits\SoftDeleteServiceTrait;

class PostService extends BaseService
{
    use SoftDeleteServiceTrait;

    /**
     * PostService constructor.
     */
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }
}
