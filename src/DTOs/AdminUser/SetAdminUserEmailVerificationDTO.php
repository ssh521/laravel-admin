<?php

namespace Ssh521\LaravelAdmin\DTOs\AdminUser;

final readonly class SetAdminUserEmailVerificationDTO
{
    public function __construct(
        public int $adminUserId,
        public bool $isVerified,
    ) {}

    public static function make(array $data): self
    {
        $isVerified = filter_var($data['isVerified'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return new self(
            adminUserId: $data['adminUserId'],
            isVerified: $isVerified ?? false,
        );
    }
}
