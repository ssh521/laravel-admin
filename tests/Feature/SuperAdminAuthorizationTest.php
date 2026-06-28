<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Tests\TestCase;

class SuperAdminAuthorizationTest extends TestCase
{
    public function test_super_admin_role_bypasses_policy_checks(): void
    {
        $superAdminRole = Role::query()
            ->where('name', 'Super Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();

        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();

        $superAdminRole->syncPermissions([]);

        $superAdmin = AdminUser::query()->create([
            'name' => 'Super Admin',
            'email' => 'super-admin@example.com',
            'password' => 'password',
        ]);
        $superAdmin->assignRole($superAdminRole);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('delete', $adminRole));
    }

    public function test_direct_super_admin_manage_permission_allows_admin_user_management_policy(): void
    {
        $admin = AdminUser::query()->create([
            'name' => 'Direct Permission Admin',
            'email' => 'direct-super-admin-manage@example.com',
            'password' => 'password',
        ]);

        $admin->givePermissionTo(
            Permission::query()
                ->where('name', 'super-admin-manage')
                ->where('guard_name', 'laravel_admin')
                ->firstOrFail()
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertTrue(Gate::forUser($admin)->allows('manageUsers'));
    }

    public function test_admin_role_name_does_not_bypass_permission_checks(): void
    {
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();
        $adminRole->syncPermissions([]);

        $admin = AdminUser::query()->create([
            'name' => 'Permission Checked Admin',
            'email' => 'permission-checked-admin@example.com',
            'password' => 'password',
        ]);
        $admin->assignRole($adminRole);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertFalse(Gate::forUser($admin)->allows('viewDashboard', 'admin'));
        $this->assertFalse(Gate::forUser($admin)->allows('viewAny', Role::class));
        $this->assertFalse(Gate::forUser($admin)->allows('viewAny', Permission::class));
        $this->assertFalse(Gate::forUser($admin)->allows('viewAny', Menu::class));
        $this->assertFalse(Gate::forUser($admin)->allows('viewAny', MenuCategory::class));

        $admin->givePermissionTo(Permission::query()
            ->where('name', 'admin-dashboard-access')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail());
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertTrue(Gate::forUser($admin)->allows('viewDashboard', 'admin'));
    }
}
