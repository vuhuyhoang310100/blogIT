<?php

namespace App\DTOs\Tag;

readonly class TagFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $tag = null,
        public ?string $sort,
        public int $perPage = config('constant.PAGINATION.PER_PAGE'),
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            tag: $data['tag'] ?? null,
            sort: $data['sort'] ?? 'latest',
            perPage: (int) ($data['per_page'] ?? 10),
        );
    }
}