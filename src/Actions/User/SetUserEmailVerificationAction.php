<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Ssh521\LaravelAdmin\DTOs\User\SetUserEmailVerificationDTO;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class SetUserEmailVerificationAction
{
    public function __construct(
        private readonly ResolvesUserModel $users,
    ) {}

    public function execute(SetUserEmailVerificationDTO $dto): object
    {
        $user = $this->users->findOrFail($dto->userId);

        if ($dto->isVerified) {
            if (! $user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()]);
            }
        } else {
            $user->forceFill(['email_verified_at' => null]);
        }

        $user->save();

        return $user;
    }
}
