<?php

namespace Ssh521\LaravelAdmin\Actions\Role;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\DTOs\Role\UpdateRoleDTO;

final class UpdateRoleAction
{
    public function execute(UpdateRoleDTO $dto): Role
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $role = Role::query()
            ->where('guard_name', $guard)
            ->findOrFail($dto->roleId);

        $role->name = $dto->name;
        $role->save();

        // 권한 동기화
        if (! empty($dto->permissionIds)) {
            $permissions = Permission::query()
                ->where('guard_name', $guard)
                ->whereIn('id', $dto->permissionIds)
                ->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return $role->load('permissions');
    }
}
