<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\SoftDeletesRepository;
use App\Repositories\Eloquent\Concerns\HandlesSoftDeletes;

abstract class SoftDeleteRepository extends BaseRepository implements SoftDeletesRepository
{
    use HandlesSoftDeletes;
}
