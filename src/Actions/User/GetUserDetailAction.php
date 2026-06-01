<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Ssh521\LaravelAdmin\DTOs\User\GetUserDetailDTO;
use Ssh521\LaravelAdmin\Models\User;

final class GetUserDetailAction
{
    public function execute(GetUserDetailDTO $dto): User
    {
        return User::with(['roles', 'permissions'])->findOrFail($dto->userId);
    }
}
