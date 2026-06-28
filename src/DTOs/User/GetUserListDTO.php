<?php

namespace Ssh521\LaravelAdmin\DTOs\User;

final readonly class GetUserListDTO
{
    public function __construct(
        public ?string $search = null,
        public string $sortField = 'name',
        public string $sortDirection = 'asc',
        public int $perPage = 10,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            sortField: $data['sortField'] ?? 'name',
            sortDirection: $data['sortDirection'] ?? 'asc',
            perPage: $data['perPage'] ?? 10,
        );
    }
}
