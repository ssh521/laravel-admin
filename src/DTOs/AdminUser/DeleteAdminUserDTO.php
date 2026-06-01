<?php

namespace Ssh521\LaravelAdmin\DTOs\AdminUser;

final readonly class DeleteAdminUserDTO
{
    public function __construct(
        public int $adminUserId,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            adminUserId: $data['adminUserId'],
        );
    }
}
