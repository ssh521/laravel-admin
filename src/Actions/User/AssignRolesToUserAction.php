<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\DTOs\User\AssignRolesToUserDTO;
use Ssh521\LaravelAdmin\Models\User;

final class AssignRolesToUserAction
{
    public function execute(AssignRolesToUserDTO $dto): User
    {
        $user = User::findOrFail($dto->userId);
        $roles = Role::whereIn('id', $dto->roleIds)->get();

        $user->syncRoles($roles);

        return $user;
    }
}
