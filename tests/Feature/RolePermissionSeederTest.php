<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Tests\TestCase;

class RolePermissionSeederTest extends TestCase
{
    public function test_it_assigns_all_default_permissions_to_super_admin_and_admin_roles(): void
    {
        $permissions = Permission::query()
            ->where('guard_name', 'laravel_admin')
            ->pluck('id')
            ->sort()
            ->values();

        $superAdminRole = Role::query()
            ->where('name', 'Super Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();

        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();

        $this->assertSame(
            $permissions->all(),
            $superAdminRole->permissions()->pluck('permissions.id')->sort()->values()->all()
        );

        $this->assertSame(
            $permissions->all(),
            $adminRole->permissions()->pluck('permissions.id')->sort()->values()->all()
        );
    }

    public function test_it_assigns_existing_package_permissions_to_super_admin_and_admin_when_reseeded(): void
    {
        $permission = Permission::query()->create([
            'name' => 'sample-package-manage',
            'guard_name' => 'laravel_admin',
        ]);

        $this->seed(\Ssh521\LaravelAdmin\Database\Seeders\RolePermissionSeeder::class);

        $superAdminRole = Role::query()
            ->where('name', 'Super Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();

        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', 'laravel_admin')
            ->firstOrFail();

        $this->assertTrue($superAdminRole->permissions()->whereKey($permission->id)->exists());
        $this->assertTrue($adminRole->permissions()->whereKey($permission->id)->exists());
    }
}
