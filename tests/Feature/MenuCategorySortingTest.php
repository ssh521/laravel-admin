<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class MenuCategorySortingTest extends TestCase
{
    public function test_menu_categories_index_uses_sort_query_parameters(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();

        $admin = AdminUser::query()->forceCreate([
            'name' => 'Menu Category Sorting Super Admin',
            'email' => 'menu-category-sorting-super-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($superAdminRole);

        MenuCategory::query()->create([
            'name' => 'Alpha Category',
            'is_active' => true,
            'sort_order' => 20,
        ]);

        MenuCategory::query()->create([
            'name' => 'Zulu Category',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->actingAs($admin, $guard)
            ->get(route('admin.menu-categories.index', [
                'sort' => 'name',
                'direction' => 'asc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Alpha Category', 'Zulu Category']);

        $this->actingAs($admin, $guard)
            ->get(route('admin.menu-categories.index', [
                'sort' => 'name',
                'direction' => 'desc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Zulu Category', 'Alpha Category']);
    }
}
