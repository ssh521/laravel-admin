<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Database\Seeders\MenuSeeder;
use Ssh521\LaravelAdmin\Livewire\Admin\LeftMenu;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class MenuCategoryRoleExposureTest extends TestCase
{
    public function test_default_admin_left_menu_includes_admin_only_categories(): void
    {
        $this->seed(MenuSeeder::class);

        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', $guard)
            ->firstOrFail();

        $admin = $this->createAdminUser('admin-menu@example.com', $adminRole);

        $categoryNames = $this->leftMenuCategoryNames($admin, $guard);

        $this->assertContains('일반', $categoryNames);
        $this->assertContains('메뉴 관리', $categoryNames);
        $this->assertContains('관리자 전용', $categoryNames);
    }

    public function test_super_admin_left_menu_includes_all_active_categories(): void
    {
        $this->seed(MenuSeeder::class);

        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();

        $superAdmin = $this->createAdminUser('super-menu@example.com', $superAdminRole);

        $categoryNames = $this->leftMenuCategoryNames($superAdmin, $guard);

        $this->assertContains('일반', $categoryNames);
        $this->assertContains('메뉴 관리', $categoryNames);
        $this->assertContains('관리자 전용', $categoryNames);
    }

    public function test_custom_role_left_menu_only_includes_categories_attached_to_that_role(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $operatorRole = Role::query()->create([
            'name' => 'Operator',
            'guard_name' => $guard,
        ]);
        $auditorRole = Role::query()->create([
            'name' => 'Auditor',
            'guard_name' => $guard,
        ]);

        $visibleCategory = $this->createCategory('Operator Tools', $operatorRole);
        $hiddenCategory = $this->createCategory('Auditor Tools', $auditorRole);
        $unassignedCategory = MenuCategory::query()->create([
            'name' => 'Unassigned Tools',
            'is_active' => true,
            'sort_order' => 3,
        ]);
        $inactiveCategory = $this->createCategory('Inactive Operator Tools', $operatorRole, false);

        $operator = $this->createAdminUser('operator-menu@example.com', $operatorRole);

        $categoryNames = $this->leftMenuCategoryNames($operator, $guard);

        $this->assertContains($visibleCategory->name, $categoryNames);
        $this->assertNotContains($hiddenCategory->name, $categoryNames);
        $this->assertNotContains($unassignedCategory->name, $categoryNames);
        $this->assertNotContains($inactiveCategory->name, $categoryNames);
    }

    /**
     * @return array<int, string>
     */
    private function leftMenuCategoryNames(AdminUser $user, string $guard): array
    {
        $categories = Livewire::actingAs($user, $guard)
            ->test(LeftMenu::class)
            ->get('sidebarMenuCategories');

        return $categories->pluck('name')->values()->all();
    }

    private function createAdminUser(string $email, Role $role): AdminUser
    {
        $user = AdminUser::query()->forceCreate([
            'name' => $role->name.' User',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function createCategory(string $name, Role $role, bool $isActive = true): MenuCategory
    {
        $category = MenuCategory::query()->create([
            'name' => $name,
            'is_active' => $isActive,
            'sort_order' => 1,
        ]);
        $category->roles()->attach($role);

        return $category;
    }
}
