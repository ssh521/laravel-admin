<?php

namespace Ssh521\LaravelAdmin\DTOs\User;

final readonly class DeleteUserDTO
{
    public function __construct(
        public int $userId,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            userId: $data['userId'],
        );
    }
}
