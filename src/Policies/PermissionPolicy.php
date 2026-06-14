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
        // Admin만 권한 목록을 볼 수 있음 (Super Admin은 Gate::before에서 처리됨)
        if ($user->hasRole('Admin')) {
            return $user->hasPermissionTo('permission-list');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Permission $permission): bool
    {
        // Admin은 Super Admin 관련 권한을 제외한 모든 권한을 볼 수 있음
        // 권한명에 super-admin이 포함된 권한들을 찾아서 제한
        // 예: super-admin-manage, super-admin-settings, super-admin-users 등

        if ($user->hasRole('Admin') && ! str_contains($permission->name, 'super-admin')) {
            return $user->hasPermissionTo('permission-view');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        // Admin만 권한을 생성할 수 있음 (Super Admin은 Gate::before에서 처리됨)
        if ($user->hasRole('Admin')) {
            return $user->hasPermissionTo('permission-create');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 수정 불가
        if (in_array($permission->name, [
            'user-list', 'user-view', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        // Admin은 Super Admin 관련 권한을 제외한 권한만 수정할 수 있음
        if ($user->hasRole('Admin') && ! str_contains($permission->name, 'super-admin')) {
            return $user->hasPermissionTo('permission-edit');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 삭제 불가
        if (in_array($permission->name, [
            'user-list', 'user-view', 'user-create', 'user-edit', 'user-delete',
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

        // Admin은 Super Admin 관련 권한을 제외한 권한만 삭제할 수 있음
        if ($user->hasRole('Admin') && ! str_contains($permission->name, 'super-admin')) {
            return $user->hasPermissionTo('permission-delete');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 복원 불가
        if (in_array($permission->name, [
            'user-list', 'user-view', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        // Admin은 Super Admin 관련 권한을 제외한 권한만 복원할 수 있음
        if ($user->hasRole('Admin') && ! str_contains($permission->name, 'super-admin')) {
            return $user->hasPermissionTo('permission-edit');
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Permission $permission): bool
    {
        // 시스템 기본 권한은 영구 삭제 불가
        if (in_array($permission->name, [
            'user-list', 'user-view', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-view', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-view', 'permission-create', 'permission-edit', 'permission-delete',
        ])) {
            return false;
        }

        // Admin은 Super Admin 관련 권한을 제외한 권한만 영구 삭제할 수 있음
        if ($user->hasRole('Admin') && ! str_contains($permission->name, 'super-admin')) {
            return $user->hasPermissionTo('permission-delete');
        }

        return false;
    }

    /**
     * Determine whether the user can assign permissions to roles.
     */
    public function assignToRoles($user, Permission $permission): bool
    {
        // Admin은 Super Admin 관련 권한을 제외한 권한만 역할에 할당할 수 있음
        if ($user->hasRole('Admin') && ! str_contains($permission->name, 'super-admin')) {
            return $user->hasPermissionTo('permission-assign-to-roles');
        }

        return false;
    }

}
