<?php

namespace Ssh521\LaravelAdmin\Actions\Role;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\DTOs\Role\GetRoleDetailDTO;

final class GetRoleDetailAction
{
    public function execute(GetRoleDetailDTO $dto): Role
    {
        return Role::query()
            ->where('guard_name', config('laravel-admin.auth.guard', 'laravel_admin'))
            ->with('permissions')
            ->findOrFail($dto->roleId);
    }
}
