<?php

namespace Ssh521\LaravelAdmin\DTOs\Role;

final readonly class UpdateRoleDTO
{
    public function __construct(
        public int $roleId,
        public string $name,
        public ?array $permissionIds = null,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            roleId: $data['roleId'],
            name: $data['name'],
            permissionIds: $data['permissionIds'] ?? null,
        );
    }
}
