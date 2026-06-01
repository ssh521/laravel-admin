<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Ssh521\LaravelAdmin\DTOs\User\SetUserEmailVerificationDTO;
use Ssh521\LaravelAdmin\Models\User;

final class SetUserEmailVerificationAction
{
    public function execute(SetUserEmailVerificationDTO $dto): User
    {
        $user = User::findOrFail($dto->userId);

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
