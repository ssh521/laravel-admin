<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
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
}
