<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        // Admin만 역할 목록을 볼 수 있음 (Super Admin은 Gate::before에서 처리됨)
        if ($user->hasRole('Admin')) {
            return $user->hasPermissionTo('role-list');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Role $role): bool
    {
        // Admin은 Super Admin 역할을 제외한 모든 역할을 볼 수 있음
        if ($user->hasRole('Admin') && $role->name !== 'Super Admin') {
            return $user->hasPermissionTo('role-view');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        // Admin만 역할을 생성할 수 있음 (Super Admin은 Gate::before에서 처리됨)
        if ($user->hasRole('Admin')) {
            return $user->hasPermissionTo('role-create');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Role $role): bool
    {
        // Admin은 Super Admin과 Admin 역할을 제외한 역할만 수정할 수 있음
        if ($user->hasRole('Admin') && ! in_array($role->name, ['Super Admin', 'Admin'])) {
            return $user->hasPermissionTo('role-edit');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Role $role): bool
    {
        // Super Admin과 Admin 역할은 삭제 불가
        if (in_array($role->name, ['Super Admin', 'Admin'])) {
            return false;
        }

        // 역할이 사용자에게 할당되어 있으면 삭제 불가
        if ($role->users()->count() > 0) {
            return false;
        }

        // Admin은 Super Admin과 Admin 역할을 제외한 역할만 삭제할 수 있음
        if ($user->hasRole('Admin') && ! in_array($role->name, ['Super Admin', 'Admin'])) {
            return $user->hasPermissionTo('role-delete');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Role $role): bool
    {
        // Super Admin과 Admin 역할은 복원 불가
        if (in_array($role->name, ['Super Admin', 'Admin'])) {
            return false;
        }

        // Admin은 Super Admin과 Admin 역할을 제외한 역할만 복원할 수 있음
        if ($user->hasRole('Admin') && ! in_array($role->name, ['Super Admin', 'Admin'])) {
            return $user->hasPermissionTo('role-edit');
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Role $role): bool
    {
        // Super Admin과 Admin 역할은 영구 삭제 불가
        if (in_array($role->name, ['Super Admin'])) {
            return false;
        }

        // Admin은 Super Admin과 Admin 역할을 제외한 역할만 영구 삭제할 수 있음
        if ($user->hasRole('Admin') && ! in_array($role->name, ['Super Admin', 'Admin'])) {
            return $user->hasPermissionTo('role-delete');
        }

        return false;
    }

    /**
     * Determine whether the user can assign permissions to the role.
     */
    public function assignPermissions($user, Role $role): bool
    {
        // Super Admin 역할의 권한은 수정 불가
        if ($role->name === 'Super Admin') {
            return false;
        }

        // Admin은 Super Admin과 Admin 역할을 제외한 역할에만 권한을 할당할 수 있음
        if ($user->hasRole('Admin') && ! in_array($role->name, ['Super Admin', 'Admin'])) {
            return $user->hasPermissionTo('role-assign-permissions');
        }

        return false;
    }

    /**
     * Determine whether the user can view role permissions.
     */
    public function viewPermissions($user, Role $role): bool
    {
        // Admin은 Super Admin 역할을 제외한 모든 역할의 권한을 볼 수 있음
        if ($user->hasRole('Admin') && $role->name !== 'Super Admin') {
            return $user->hasPermissionTo('role-view-permissions');
        }

        return false;
    }
}
