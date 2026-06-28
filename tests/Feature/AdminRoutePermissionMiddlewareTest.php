<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminRoutePermissionMiddlewareTest extends TestCase
{
    public function test_admin_web_routes_use_seeded_route_level_permissions(): void
    {
        $expected = [
            'admin.index' => 'admin-dashboard-access',
            'admin.admin-users.index' => 'super-admin-manage',
            'admin.ui.components' => 'super-admin-manage',
            'admin.users.index' => 'user-list',
            'admin.users.table' => 'user-list',
            'admin.users.create' => 'user-create',
            'admin.users.select-user' => 'user-list',
            'admin.users.edit' => 'user-edit',
            'admin.users.show' => 'user-view',
            'admin.users.store' => 'user-create',
            'admin.users.update' => 'user-edit',
            'admin.users.destroy' => 'user-delete',
            'admin.roles.index' => 'role-list',
            'admin.roles.create' => 'role-create',
            'admin.roles.store' => 'role-create',
            'admin.roles.show' => 'role-view',
            'admin.roles.edit' => 'role-edit',
            'admin.roles.update' => 'role-edit',
            'admin.roles.destroy' => 'role-delete',
            'admin.permissions.index' => 'permission-list',
            'admin.permissions.create' => 'permission-create',
            'admin.permissions.store' => 'permission-create',
            'admin.permissions.update-order' => 'permission-edit',
            'admin.permissions.show' => 'permission-view',
            'admin.permissions.edit' => 'permission-edit',
            'admin.permissions.update' => 'permission-edit',
            'admin.permissions.destroy' => 'permission-delete',
            'admin.menu-categories.index' => 'menu-category-list',
            'admin.menu-categories.create' => 'menu-category-create',
            'admin.menu-categories.store' => 'menu-category-create',
            'admin.menu-categories.edit' => 'menu-category-edit',
            'admin.menu-categories.update' => 'menu-category-edit',
            'admin.menu-categories.destroy' => 'menu-category-delete',
            'admin.menu-categories.toggle-status' => 'menu-category-edit',
            'admin.menu-categories.update-order' => 'menu-category-edit',
            'admin.menu-categories.update-order-multiple' => 'menu-category-edit',
            'admin.menu-categories.roles' => 'menu-category-edit',
            'admin.menu-categories.roles.update' => 'menu-category-edit',
            'admin.menu-categories.get-roles' => 'menu-category-view',
            'admin.menu-categories.update-roles-api' => 'menu-category-edit',
            'admin.menu-categories.get-roles-modal' => 'menu-category-view',
            'admin.menu-categories.show' => 'menu-category-view',
            'admin.menus.index' => 'menu-list',
            'admin.menus.create' => 'menu-create',
            'admin.menus.store' => 'menu-create',
            'admin.menus.search' => 'menu-list',
            'admin.menus.category' => 'menu-view',
            'admin.menus.update-order-multiple' => 'menu-edit',
            'admin.menus.update-category-multiple' => 'menu-edit',
            'admin.menus.edit' => 'menu-edit',
            'admin.menus.update' => 'menu-edit',
            'admin.menus.destroy' => 'menu-delete',
            'admin.menus.toggle-status' => 'menu-edit',
            'admin.menus.update-order' => 'menu-edit',
            'admin.menus.show' => 'menu-view',
        ];

        foreach ($expected as $routeName => $permission) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "Expected route [{$routeName}] to exist.");
            $this->assertContains("can:{$permission}", $route->gatherMiddleware(), "Expected route [{$routeName}] to require [{$permission}].");
            $this->assertTrue(
                Permission::query()
                    ->where('guard_name', config('laravel-admin.auth.guard', 'laravel_admin'))
                    ->where('name', $permission)
                    ->exists(),
                "Expected permission [{$permission}] to be seeded."
            );
        }
    }

    public function test_admin_without_required_permission_cannot_access_route_level_protected_admin_routes(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', $guard)
            ->firstOrFail();
        $adminRole->syncPermissions([]);

        $admin = AdminUser::query()->forceCreate([
            'name' => 'Admin User',
            'email' => 'admin-route-denied@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($adminRole);

        $this->actingAs($admin, $guard)
            ->getJson(route('admin.menus.search', ['q' => 'missing']))
            ->assertForbidden();
    }

    public function test_super_admin_bypasses_route_level_permission_middleware(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();
        $superAdminRole->syncPermissions([]);

        $superAdmin = AdminUser::query()->forceCreate([
            'name' => 'Super Admin',
            'email' => 'super-route-allowed@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $superAdmin->assignRole($superAdminRole);

        $this->actingAs($superAdmin, $guard)
            ->getJson(route('admin.menus.search', ['q' => 'missing']))
            ->assertOk()
            ->assertExactJson([]);
    }
}
