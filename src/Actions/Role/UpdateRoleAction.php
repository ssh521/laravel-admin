<?php

namespace Ssh521\LaravelAdmin\Actions\Role;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\DTOs\Role\UpdateRoleDTO;

final class UpdateRoleAction
{
    public function execute(UpdateRoleDTO $dto): Role
    {
        $role = Role::findOrFail($dto->roleId);

        $role->name = $dto->name;
        $role->save();

        // 권한 동기화
        if (! empty($dto->permissionIds)) {
            $permissions = Permission::whereIn('id', $dto->permissionIds)->pluck('name');
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return $role->load('permissions');
    }
}
