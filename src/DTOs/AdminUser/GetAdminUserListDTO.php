<?php

namespace Ssh521\LaravelAdmin\DTOs\AdminUser;

final readonly class GetAdminUserListDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $role = null,
        public string $sortField = 'name',
        public string $sortDirection = 'asc',
        public int $perPage = 10,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            role: $data['role'] ?? null,
            sortField: $data['sortField'] ?? 'name',
            sortDirection: $data['sortDirection'] ?? 'asc',
            perPage: $data['perPage'] ?? 10,
        );
    }
}
