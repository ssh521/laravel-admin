<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class MenuSearchTest extends TestCase
{
    public function test_menu_search_only_returns_categories_available_to_the_current_user(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', $guard)
            ->firstOrFail();
        $restrictedRole = Role::query()->create([
            'name' => 'Restricted',
            'guard_name' => $guard,
        ]);

        $visibleCategory = $this->createCategoryWithRole('Visible Tools', $adminRole);
        $restrictedCategory = $this->createCategoryWithRole('Restricted Tools', $restrictedRole);

        $this->createMenu($visibleCategory, 'Shared Reports');
        $this->createMenu($restrictedCategory, 'Shared Secrets');

        $user = AdminUser::query()->forceCreate([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $user->assignRole($adminRole);

        $response = $this->actingAs($user, $guard)
            ->getJson(route('admin.menus.search', ['q' => 'Shared']));

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Shared Reports']);
        $response->assertJsonMissing(['name' => 'Shared Secrets']);
    }

    public function test_menu_search_excludes_inactive_categories(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();

        $activeCategory = $this->createCategoryWithRole('Active Tools', $superAdminRole);
        $inactiveCategory = $this->createCategoryWithRole('Inactive Tools', $superAdminRole, false);

        $this->createMenu($activeCategory, 'Operational Report');
        $this->createMenu($inactiveCategory, 'Operational Archive');

        $user = AdminUser::query()->forceCreate([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $user->assignRole($superAdminRole);

        $response = $this->actingAs($user, $guard)
            ->getJson(route('admin.menus.search', ['q' => 'Operational']));

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Operational Report']);
        $response->assertJsonMissing(['name' => 'Operational Archive']);
    }

    private function createCategoryWithRole(string $name, Role $role, bool $isActive = true): MenuCategory
    {
        $category = MenuCategory::query()->create([
            'name' => $name,
            'is_active' => $isActive,
            'sort_order' => 1,
        ]);
        $category->roles()->attach($role);

        return $category;
    }

    private function createMenu(MenuCategory $category, string $name): Menu
    {
        return Menu::query()->create([
            'name' => $name,
            'url' => '/admin/'.str($name)->slug('-'),
            'category_id' => $category->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
