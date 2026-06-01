<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Ssh521\LaravelAdmin\DTOs\User\DeleteUserDTO;
use Ssh521\LaravelAdmin\Models\User;

final class DeleteUserAction
{
    public function execute(DeleteUserDTO $dto): bool
    {
        $user = User::findOrFail($dto->userId);
        $user->delete();

        return true;
    }
}
