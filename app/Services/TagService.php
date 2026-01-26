<?php

namespace App\Services;

use App\DTOs\Tag\CreateTagDTO;
use App\DTOs\Tag\TagFilterDTO;
use App\DTOs\Tag\UpdateTagDTO;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TagService
{
    protected $tagRepository;

    /**
     * Inject Tag repository
     *
     * @param TagRepositoryInterface
     */
    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Get all tags with params
     */
    public function getAll(TagFilterDTO $dto): LengthAwarePaginator
    {
        return $this->tagRepository->paginate(filters: $dto->filters);
    }

    /**
     * Store a new tag
     */
    public function store(CreateTagDTO $dto): void
    {
        $this->tagRepository->create([
            'name' => $dto->name,
        ]);
    }

    /**
     * Update an existing tag
     */
    public function update(int $id, UpdateTagDTO $dto): void
    {
        $this->tagRepository->update($id, [
            'name' => $dto->name,
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
