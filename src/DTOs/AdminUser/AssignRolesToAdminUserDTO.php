<?php

namespace Ssh521\LaravelAdmin\DTOs\AdminUser;

final readonly class AssignRolesToAdminUserDTO
{
    public function __construct(
        public int $adminUserId,
        public array $roleIds,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            adminUserId: $data['adminUserId'],
            roleIds: $data['roleIds'],
        );
    }
}
