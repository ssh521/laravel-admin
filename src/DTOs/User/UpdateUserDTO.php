<?php

namespace Ssh521\LaravelAdmin\DTOs\User;

final readonly class UpdateUserDTO
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $email,
        public ?string $password = null,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            userId: $data['userId'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
        );
    }
}
