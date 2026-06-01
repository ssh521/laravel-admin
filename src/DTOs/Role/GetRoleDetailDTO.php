<?php

namespace Ssh521\LaravelAdmin\DTOs\Role;

final readonly class GetRoleDetailDTO
{
    public function __construct(
        public int $roleId,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            roleId: $data['roleId'],
        );
    }
}
