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
        return $user->hasPermissionTo('role-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('role-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return $user->hasPermissionTo('role-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('role-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        // 역할이 사용자에게 할당되어 있으면 삭제 불가
        if ($role->users()->count() > 0) {
            return false;
        }

        return $user->hasPermissionTo('role-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('role-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('role-delete');
    }

    /**
     * Determine whether the user can assign permissions to the role.
     */
    public function assignPermissions($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('role-assign-permissions');
    }

    /**
     * Reserve promotion to the configured Super Admin role for the global Gate override.
     */
    public function promoteToSuperAdmin(object $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view role permissions.
     */
    public function viewPermissions($user, Role $role): bool
    {
        if ($role->name === config('laravel-admin.super_admin_role', 'Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('role-view-permissions');
    }
}
