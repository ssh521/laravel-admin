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
        // Super Admin은 항상 접근 가능
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // 관리자 역할을 가진 사용자만 접근 가능
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 특정 권한을 가진 사용자도 접근 가능
        return $user->hasPermissionTo('admin-dashboard-access');
    }

    /**
     * Determine whether the user can view admin statistics.
     */
    public function viewStatistics($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-view-statistics');
    }

    /**
     * Determine whether the user can view admin reports.
     */
    public function viewReports($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-view-reports');
    }

    /**
     * Determine whether the user can manage system settings.
     */
    public function manageSettings($user): bool
    {
        // 시스템 설정은 Super Admin만 관리 가능
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can view admin logs.
     */
    public function viewLogs($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-view-logs');
    }

    /**
     * Determine whether the user can export admin data.
     */
    public function exportData($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-export-data');
    }

    /**
     * Determine whether the user can access admin menu management.
     */
    public function manageMenus($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-manage-menus');
    }

    /**
     * Determine whether the user can access admin user management.
     */
    public function manageUsers($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-manage-users');
    }

    /**
     * Determine whether the user can access admin role management.
     */
    public function manageRoles($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-manage-roles');
    }

    /**
     * Determine whether the user can access admin permission management.
     */
    public function managePermissions($user): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasPermissionTo('admin-manage-permissions');
    }
}
