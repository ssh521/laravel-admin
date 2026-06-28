<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('permission-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Permission $permission): bool
    {
        if (str_contains($permission->name, 'super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('permission-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return $user->hasPermissionTo('permission-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 수정 불가
        if (in_array($permission->name, [
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        if (str_contains($permission->name, 'super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('permission-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 삭제 불가
        if (in_array($permission->name, [
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        // 권한이 역할에 할당되어 있으면 삭제 불가
        if ($permission->roles()->count() > 0) {
            return false;
        }

        // 권한이 사용자에게 직접 할당되어 있으면 삭제 불가
        if ($permission->users()->count() > 0) {
            return false;
        }

        if (str_contains($permission->name, 'super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('permission-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 복원 불가
        if (in_array($permission->name, [
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        if (str_contains($permission->name, 'super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('permission-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 영구 삭제 불가
        if (in_array($permission->name, [
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        if (str_contains($permission->name, 'super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('permission-delete');
    }

    /**
     * Determine whether the user can assign permissions to roles.
     */
    public function assignToRoles($user, Permission $permission): bool
    {
        if (str_contains($permission->name, 'super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('permission-assign-to-roles');
    }

}
