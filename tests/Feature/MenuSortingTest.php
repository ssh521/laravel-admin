<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class MenuSortingTest extends TestCase
{
    public function test_menus_index_uses_sort_query_parameters(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();

        $admin = AdminUser::query()->forceCreate([
            'name' => 'Menu Sorting Super Admin',
            'email' => 'menu-sorting-super-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($superAdminRole);

        $category = MenuCategory::query()->create([
            'name' => 'Menu Sorting Category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Menu::query()->create([
            'name' => 'Alpha Menu',
            'category_id' => $category->id,
            'route_name' => 'admin.alpha',
            'url' => '#',
            'is_active' => true,
            'sort_order' => 20,
        ]);

        Menu::query()->create([
            'name' => 'Zulu Menu',
            'category_id' => $category->id,
            'route_name' => 'admin.zulu',
            'url' => '#',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->actingAs($admin, $guard)
            ->get(route('admin.menus.index', [
                'sort' => 'name',
                'direction' => 'asc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Alpha Menu', 'Zulu Menu']);

        $this->actingAs($admin, $guard)
            ->get(route('admin.menus.index', [
                'sort' => 'name',
                'direction' => 'desc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Zulu Menu', 'Alpha Menu']);
    }
}
