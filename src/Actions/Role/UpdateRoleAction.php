<?php

namespace Ssh521\LaravelAdmin\Actions\Role;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\DTOs\Role\UpdateRoleDTO;
use Ssh521\LaravelAdmin\Support\RolePermissionAssignment;

final class UpdateRoleAction
{
    public function __construct(private readonly RolePermissionAssignment $permissionAssignment) {}

    public function execute(UpdateRoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto): Role {
            $role = Role::query()
                ->where('guard_name', config('laravel-admin.auth.guard', 'laravel_admin'))
                ->lockForUpdate()
                ->findOrFail($dto->roleId);

            $permissions = $this->permissionAssignment->authorize($role, $dto->name, $dto->permissionIds);

            $role->update(['name' => $dto->name]);

            if ($permissions !== null) {
                $role->syncPermissions($permissions);
            }

            return $role->load('permissions');
        });
    }
}
