<?php

namespace Ssh521\LaravelAdmin\DTOs\User;

final readonly class AssignRolesToUserDTO
{
    public function __construct(
        public int $userId,
        public array $roleIds,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            userId: $data['userId'],
            roleIds: $data['roleIds'],
        );
    }
}
