<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

final readonly class GetNoticeListDTO
{
    public function __construct(
        public ?string $search = null,
        public ?bool $isPublished = null,
        public string $sortField = 'created_at',
        public string $sortDirection = 'desc',
        public int $perPage = 10,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            isPublished: isset($data['is_published']) ? (bool) $data['is_published'] : null,
            sortField: $data['sortField'] ?? 'created_at',
            sortDirection: $data['sortDirection'] ?? 'desc',
            perPage: $data['perPage'] ?? 10,
        );
    }
}
