<?php

namespace Ssh521\LaravelAdmin\DTOs\User;

final readonly class SetUserEmailVerificationDTO
{
    public function __construct(
        public int $userId,
        public bool $isVerified,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function make(array $data): self
    {
        $isVerified = filter_var($data['isVerified'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return new self(
            userId: $data['userId'],
            isVerified: $isVerified ?? false,
        );
    }
}
