<?php

namespace App\Repositories;
use App\Models\Handlepdf;
use Illuminate\Pagination\LengthAwarePaginator;

class HandlepdfRepository
{
    /**
     * The Handlepdf model instance.
     */
    protected Handlepdf $model;

    /**
     * HandlepdfRepository constructor.
     */
    public function __construct(Handlepdf $model)
    {
        $this->model = $model;
    }

    /**
     * Get all category by params
     */
    public function getAll($onlyRoot = false): LengthAwarePaginator
    {
        return $this->model
        ->with('user')
        ->orderBy('id', 'desc')
        ->paginate(10);
    }
}
