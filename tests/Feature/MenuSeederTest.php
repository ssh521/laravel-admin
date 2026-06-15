<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Ssh521\LaravelAdmin\Database\Seeders\MenuSeeder;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class MenuSeederTest extends TestCase
{
    public function test_it_places_role_and_permission_menus_under_admin_only_category(): void
    {
        $this->seed(MenuSeeder::class);

        $memberCategory = MenuCategory::query()->where('name', '회원 관리')->firstOrFail();
        $adminOnlyCategory = MenuCategory::query()->where('name', '관리자 전용')->firstOrFail();

        Menu::query()
            ->whereIn('name', ['역할', '권한'])
            ->update(['category_id' => $memberCategory->id]);

        $this->seed(MenuSeeder::class);

        $memberCategory = $memberCategory->fresh();
        $adminOnlyCategory = $adminOnlyCategory->fresh();

        $this->assertSame($memberCategory->id, Menu::query()->where('name', '회원')->value('category_id'));
        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', '관리자 계정')->value('category_id'));
        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', '역할')->value('category_id'));
        $this->assertSame($adminOnlyCategory->id, Menu::query()->where('name', '권한')->value('category_id'));
        $this->assertSame(1, $memberCategory->fresh()->menu_count);
        $this->assertSame(3, $adminOnlyCategory->fresh()->menu_count);
    }
}
