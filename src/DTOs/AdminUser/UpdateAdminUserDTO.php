<?php

namespace Ssh521\LaravelAdmin\DTOs\AdminUser;

final readonly class UpdateAdminUserDTO
{
    public function __construct(
        public int $adminUserId,
        public string $name,
        public string $email,
        public ?string $password = null,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            adminUserId: $data['adminUserId'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
        );
    }
}
