<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access admin dashboard.
     */
    public function viewDashboard($user): bool
    {
        return $user->hasPermissionTo('admin-dashboard-access');
    }

    /**
     * Determine whether the user can view admin statistics.
     */
    public function viewStatistics($user): bool
    {
        return $user->hasPermissionTo('admin-view-statistics');
    }

    /**
     * Determine whether the user can view admin reports.
     */
    public function viewReports($user): bool
    {
        return $user->hasPermissionTo('admin-view-reports');
    }

    /**
     * Determine whether the user can manage system settings.
     */
    public function manageSettings($user): bool
    {
        return $user->hasPermissionTo('super-admin-settings');
    }

    /**
     * Determine whether the user can view admin logs.
     */
    public function viewLogs($user): bool
    {
        return $user->hasPermissionTo('admin-view-logs');
    }

    /**
     * Determine whether the user can export admin data.
     */
    public function exportData($user): bool
    {
        return $user->hasPermissionTo('admin-export-data');
    }

    /**
     * Determine whether the user can access admin menu management.
     */
    public function manageMenus($user): bool
    {
        return $user->hasPermissionTo('admin-manage-menus');
    }

    /**
     * Determine whether the user can access admin user management.
     */
    public function manageUsers($user): bool
    {
        return $user->hasPermissionTo('super-admin-manage');
    }

    /**
     * Determine whether the user can access admin role management.
     */
    public function manageRoles($user): bool
    {
        return $user->hasPermissionTo('admin-manage-roles');
    }

    /**
     * Determine whether the user can access admin permission management.
     */
    public function managePermissions($user): bool
    {
        return $user->hasPermissionTo('admin-manage-permissions');
    }
}
