<?php

namespace Ssh521\LaravelAdmin\DTOs\AdminUser;

final readonly class CreateAdminUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public bool $isVerified = false,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            isVerified: (bool) ($data['isVerified'] ?? false),
        );
    }
}
