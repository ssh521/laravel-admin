<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminUserSortingTest extends TestCase
{
    public function test_admin_users_index_uses_sort_query_parameters(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();

        $admin = AdminUser::query()->forceCreate([
            'name' => 'Sorting Super Admin',
            'email' => 'sorting-super-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($superAdminRole);

        AdminUser::query()->forceCreate([
            'name' => 'Alpha Admin',
            'email' => 'alpha-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        AdminUser::query()->forceCreate([
            'name' => 'Zulu Admin',
            'email' => 'zulu-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        $this->actingAs($admin, $guard)
            ->get(route('admin.admin-users.index', [
                'sortField' => 'name',
                'sortDirection' => 'asc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Alpha Admin', 'Zulu Admin']);

        $this->actingAs($admin, $guard)
            ->get(route('admin.admin-users.index', [
                'sortField' => 'name',
                'sortDirection' => 'desc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Zulu Admin', 'Alpha Admin']);
    }
}
