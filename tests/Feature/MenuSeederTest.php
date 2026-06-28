<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Database\Seeders\MenuSeeder;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class MenuSeederTest extends TestCase
{
    public function test_it_places_reference_role_and_permission_menus_under_admin_only_category(): void
    {
        $this->seed(MenuSeeder::class);

        $menuCategory = MenuCategory::query()->where('name', '메뉴 관리')->firstOrFail();
        $adminOnlyCategory = MenuCategory::query()->where('name', '관리자 전용')->firstOrFail();

        Menu::query()
            ->whereIn('name', ['UI 컴포넌트', '역할', '권한'])
            ->update(['category_id' => $menuCategory->id]);

        $this->seed(MenuSeeder::class);

        $menuCategory = $menuCategory->fresh();
        $adminOnlyCategory = $adminOnlyCategory->fresh();

        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', '관리자 계정')->value('category_id'));
        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', 'UI 컴포넌트')->value('category_id'));
        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', '역할')->value('category_id'));
        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', '권한')->value('category_id'));
        $this->assertSame(2, $menuCategory->fresh()->menu_count);
        $this->assertSame(4, $adminOnlyCategory->fresh()->menu_count);
    }

    public function test_it_exposes_admin_only_menu_category_to_super_admin_only(): void
    {
        $this->seed(MenuSeeder::class);

        $generalCategory = MenuCategory::query()->where('name', '일반')->firstOrFail();
        $menuCategory = MenuCategory::query()->where('name', '메뉴 관리')->firstOrFail();
        $adminOnlyCategory = MenuCategory::query()->where('name', '관리자 전용')->firstOrFail();
        $superAdminRole = Role::query()->where('name', 'Super Admin')->where('guard_name', 'laravel_admin')->firstOrFail();
        $adminRole = Role::query()->where('name', 'Admin')->where('guard_name', 'laravel_admin')->firstOrFail();

        $this->assertTrue($generalCategory->roles()->whereKey($superAdminRole->id)->exists());
        $this->assertTrue($menuCategory->roles()->whereKey($superAdminRole->id)->exists());
        $this->assertTrue($adminOnlyCategory->roles()->whereKey($superAdminRole->id)->exists());

        $this->assertTrue($generalCategory->roles()->whereKey($adminRole->id)->exists());
        $this->assertTrue($menuCategory->roles()->whereKey($adminRole->id)->exists());
        $this->assertFalse($adminOnlyCategory->roles()->whereKey($adminRole->id)->exists());
    }
}
