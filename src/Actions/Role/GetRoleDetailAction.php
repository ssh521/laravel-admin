<?php

namespace Ssh521\LaravelAdmin\Actions\Role;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\DTOs\Role\GetRoleDetailDTO;

final class GetRoleDetailAction
{
    public function execute(GetRoleDetailDTO $dto): Role
    {
        return Role::with('permissions')->findOrFail($dto->roleId);
    }
}
