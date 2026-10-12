<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\Permission;

final class RolePermissionAssignment
{
    /**
     * @param  array<int, int|string>|null  $permissionIds
     * @return Collection<int, Permission>|null
     */
    public function authorize(Role $role, string $name, ?array $permissionIds): ?Collection
    {
        if ($name === config('laravel-admin.super_admin_role', 'Super Admin')
            && (! $role->exists || $role->name !== $name)) {
            Gate::authorize('promoteToSuperAdmin', $role);
        }

        if ($permissionIds === null) {
            return null;
        }

        $selectedIds = array_values(array_unique(array_map(intval(...), $permissionIds)));
        $permissions = Permission::query()
            ->where('guard_name', $role->guard_name)
            ->whereIn('id', $selectedIds)
            ->get();

        if ($permissions->count() !== count($selectedIds)) {
            throw ValidationException::withMessages(['permissions' => '선택한 권한을 확인해 주세요.']);
        }

        $currentIds = $role->exists ? $role->permissions()->get()->modelKeys() : [];
        $changedIds = array_values(array_unique(array_merge(
            array_diff($selectedIds, $currentIds),
            array_diff($currentIds, $selectedIds),
        )));

        if ($changedIds !== []) {
            Gate::authorize('assignPermissions', $role);

            $changedPermissions = Permission::query()
                ->where('guard_name', $role->guard_name)
                ->whereIn('id', $changedIds)
                ->get();

            if ($changedPermissions->count() !== count($changedIds)) {
                throw ValidationException::withMessages(['permissions' => '변경할 권한을 확인해 주세요.']);
            }

            foreach ($changedPermissions as $permission) {
                Gate::authorize('assignToRoles', $permission);
            }
        }

        return $permissions;
    }
}
