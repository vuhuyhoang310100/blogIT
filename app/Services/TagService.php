<?php

namespace App\Services;

use App\DTOs\Tag\CreateTagDTO;
use App\DTOs\Tag\UpdateTagDTO;
use App\Repositories\TagRepository;

class TagService
{
    protected $tagRepository;

    /**
     * Inject Tag repository
     *
     * @param TagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Get all tags with params
     */
    public function getAll(array $filters = [], ?string $searchQuery = null)
    {
        return $this->tagRepository->getAll($filters, $searchQuery);
    }

    /**
     * Store a new tag
     */
    public function store(CreateTagDTO $dto): void
    {
        $this->tagRepository->create([
            'name' => $dto->name,
            'slug' => $dto->slug,
        ]);
    }

    /**
     * Update an existing tag
     */
    public function update(int $id, UpdateTagDTO $dto): void
    {
        $this->tagRepository->update($id, [
            'name' => $dto->name,
            'slug' => $dto->slug,
        ]);
    }

    /**
     * Delete an existing tag
     */
    public function delete(int $id): void
    {
        $this->tagRepository->delete($id);
    }
}
