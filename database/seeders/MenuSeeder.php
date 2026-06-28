<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Support\AdminMenuRegistrar;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app(AdminMenuRegistrar::class);
        $categories = [
            'general' => [
                'name' => '일반',
                'sort_order' => 1,
                'is_active' => true,
            ],
            'menu' => [
                'name' => '메뉴 관리',
                'sort_order' => 2,
                'is_active' => true,
            ],
            'admin_only' => [
                'name' => '관리자 전용',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $key => $categoryData) {
            $category = $registrar->registerCategory($categoryData);
            $categoryMap[$key] = $category->id;
        }

        $menus = [
            [
                'name' => '홈',
                'route_name' => 'home',
                'url' => '/',
                'target' => '_self',
                'category_id' => $categoryMap['general'],
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'house',
                'description' => '사이트 홈페이지',
            ],
            [
                'name' => '관리자 홈',
                'route_name' => 'admin.index',
                'url' => '/admin',
                'target' => '_self',
                'category_id' => $categoryMap['general'],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'gauge',
                'description' => '관리자 대시보드',
            ],
            [
                'name' => '대시보드',
                'route_name' => 'dashboard',
                'url' => '/dashboard',
                'target' => '_self',
                'category_id' => $categoryMap['general'],
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'icon' => 'gauge',
                'description' => '사용자 대시보드',
            ],

            // 관리자 전용 카테고리
            [
                'name' => '관리자 계정',
                'route_name' => 'admin.admin-users.index',
                'url' => '/admin/admin-users',
                'target' => '_self',
                'category_id' => $categoryMap['admin_only'],
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'user-shield',
                'description' => '관리자 계정 관리',
            ],
            [
                'name' => 'UI 컴포넌트',
                'route_name' => 'admin.ui.components',
                'url' => '/admin/ui/components',
                'target' => '_self',
                'category_id' => $categoryMap['admin_only'],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'swatchbook',
                'description' => '관리자 UI 컴포넌트 참고',
            ],
            [
                'name' => '역할',
                'route_name' => 'admin.roles.index',
                'url' => '/admin/roles',
                'target' => '_self',
                'category_id' => $categoryMap['admin_only'],
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'icon' => 'user-tag',
                'description' => '사용자 역할 관리',
            ],
            [
                'name' => '권한',
                'route_name' => 'admin.permissions.index',
                'url' => '/admin/permissions',
                'target' => '_self',
                'category_id' => $categoryMap['admin_only'],
                'parent_id' => null,
                'sort_order' => 3,
                'is_active' => true,
                'icon' => 'key',
                'description' => '시스템 권한 관리',
            ],

            // 메뉴 관리 카테고리
            [
                'name' => '메뉴 리스트',
                'route_name' => 'admin.menus.index',
                'url' => '/admin/menus',
                'target' => '_self',
                'category_id' => $categoryMap['menu'],
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'bars',
                'description' => '메뉴 항목 관리',
            ],
            [
                'name' => '메뉴 폴더',
                'route_name' => 'admin.menu-categories.index',
                'url' => '/admin/menu-categories',
                'target' => '_self',
                'category_id' => $categoryMap['menu'],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'folder',
                'description' => '메뉴 카테고리 관리',
            ],

        ];

        foreach ($menus as $menuData) {
            $category = MenuCategory::query()->find($menuData['category_id']);

            if (! $category) {
                continue;
            }

            $registrar->registerMenu($category, $menuData);
        }

        foreach ($categoryMap as $categoryId) {
            MenuCategory::whereKey($categoryId)->update([
                'menu_count' => Menu::where('category_id', $categoryId)->count(),
            ]);
        }

        $this->seedMenuCategoryRoles($categoryMap);

        $this->command?->info('메뉴 데이터가 성공적으로 생성되었습니다.');
    }

    /**
     * @param  array<string, int>  $categoryMap
     */
    private function seedMenuCategoryRoles(array $categoryMap): void
    {
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');

        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guardName)
            ->first();

        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', $guardName)
            ->first();

        if (! $superAdminRole && ! $adminRole) {
            $this->command?->warn('RolePermissionSeeder를 먼저 실행하면 메뉴 카테고리 역할이 할당됩니다.');

            return;
        }

        $generalCategoryId = $categoryMap['general'] ?? null;
        $adminOnlyCategoryId = $categoryMap['admin_only'] ?? null;
        $menuCategoryId = $categoryMap['menu'] ?? null;

        if ($superAdminRole) {
            $superAdminCategoryIds = array_filter([$generalCategoryId, $adminOnlyCategoryId, $menuCategoryId]);

            foreach ($superAdminCategoryIds as $categoryId) {
                MenuCategory::find($categoryId)?->roles()->syncWithoutDetaching([$superAdminRole->id]);
            }
        }

        if ($adminRole) {
            $adminCategoryIds = array_filter([$generalCategoryId, $adminOnlyCategoryId, $menuCategoryId]);

            foreach ($adminCategoryIds as $categoryId) {
                MenuCategory::find($categoryId)?->roles()->syncWithoutDetaching([$adminRole->id]);
            }
        }
    }
}
