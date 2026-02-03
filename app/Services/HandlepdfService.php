<?php

namespace App\Services;
use App\Repositories\HandlepdfRepository;

class HandlepdfService
{
    protected $handlepdfRepository;
    public function __construct( HandlepdfRepository $handlepdfRepository )
    {
        $this->handlepdfRepository = $handlepdfRepository;
    }

    /**
     * Get all category with params
     *
     * @param  bool  $onlyRoot
     */
    public function getAll($onlyRoot = false)
    {
        return $this->handlepdfRepository->getAll($onlyRoot);
    }

    /**
     * Store a new category
     */
    public function store(): void
    {
    }

    /**
     * Update an existing category
     */
    public function update(): void
    {
    }

    /**
     * Delete an existing category
     */
    public function delete(int $id): void
    {
    }
}
