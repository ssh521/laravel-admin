<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuSeeder extends Seeder
{
    // 실행방법
    // php artisan db:seed --class=MenuSeeder

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기존 데이터 삭제 (truncate 순서와 무관하게 하려면 FK 제약 일시 비활성화 — 드라이버별 처리는 Schema가 담당)
        Schema::disableForeignKeyConstraints();

        try {
            DB::table('menu_category_role')->truncate();
            Menu::truncate();
            MenuCategory::truncate();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // 메뉴 카테고리 생성
        $categories = [
            [
                'name' => '일반',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => '회원 관리',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => '메뉴 관리',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => '개발도구',
                'sort_order' => 9,
                'is_active' => true,
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $index => $categoryData) {
            $category = MenuCategory::create($categoryData);
            $categoryMap[$index + 1] = $category->id;
        }

        // 메뉴 생성
        $menus = [
            // 일반 카테고리 (category_id: 1)
            [
                'name' => '홈',
                'route_name' => 'home',
                'url' => '/',
                'target' => '_self',
                'category_id' => $categoryMap[1],
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'fas fa-home',
                'description' => '사이트 홈페이지',
            ],
            [
                'name' => '관리자 홈',
                'route_name' => 'admin.index',
                'url' => '/admin',
                'target' => '_self',
                'category_id' => $categoryMap[1],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'fas fa-tachometer-alt',
                'description' => '관리자 대시보드',
            ],
            [
                'name' => '대시보드',
                'route_name' => 'dashboard',
                'url' => '/dashboard',
                'target' => '_self',
                'category_id' => $categoryMap[1],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'fas fa-tachometer-alt',
                'description' => '사용자 대시보드',
            ],

            // 회원 관리 카테고리 (category_id: 2)
            [
                'name' => '회원',
                'route_name' => 'admin.users.index',
                'url' => '#',
                'target' => '_self',
                'category_id' => $categoryMap[2],
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'fas fa-user',
                'description' => 'users.index 페이지',
            ],
            [
                'name' => '역할',
                'route_name' => 'roles.index',
                'url' => '/admin/roles',
                'target' => '_self',
                'category_id' => $categoryMap[2],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'fas fa-user-tag',
                'description' => '사용자 역할 관리',
            ],
            [
                'name' => '권한',
                'route_name' => 'permissions.index',
                'url' => '/admin/permissions',
                'target' => '_self',
                'category_id' => $categoryMap[2],
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'icon' => 'fas fa-key',
                'description' => '시스템 권한 관리',
            ],

            // 메뉴 관리 카테고리 (category_id: 3)
            [
                'name' => '메뉴 리스트',
                'route_name' => 'menus.index',
                'url' => '/admin/menus',
                'target' => '_self',
                'category_id' => $categoryMap[3],
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'fas fa-bars',
                'description' => '메뉴 항목 관리',
            ],
            [
                'name' => '메뉴 폴더',
                'route_name' => 'menu-categories.index',
                'url' => '/admin/menu-categories',
                'target' => '_self',
                'category_id' => $categoryMap[3],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'fas fa-folder',
                'description' => '메뉴 카테고리 관리',
            ],

            // 개발도구 카테고리 (category_id: 4)
            [
                'name' => 'contextmenu',
                'route_name' => 'alpine.contextmenu',
                'url' => null,
                'target' => '_self',
                'category_id' => $categoryMap[4],
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'icon' => null,
                'description' => null,
            ],
            [
                'name' => 'Api Tokens Index',
                'route_name' => 'api-tokens.index',
                'url' => '/user/api-tokens',
                'target' => '_self',
                'category_id' => $categoryMap[4],
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'icon' => 'fas fa-list',
                'description' => 'api-tokens.index 페이지',
            ],
            [
                'name' => 'Alpine 테스트',
                'route_name' => 'alpine.index',
                'url' => '/alpine',
                'target' => '_self',
                'category_id' => $categoryMap[4],
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'icon' => 'fas fa-code',
                'description' => 'Alpine.js 테스트 페이지',
            ],
            [
                'name' => 'Admin Notice Index',
                'route_name' => 'admin.notices.index',
                'url' => null,
                'target' => '_self',
                'category_id' => $categoryMap[1],
                'parent_id' => null,
                'sort_order' => 4,
                'is_active' => true,
                'icon' => 'fas fa-list',
                'description' => 'Admin Notice Index 페이지',
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::create($menuData);
        }

        $this->command->info('메뉴 데이터가 성공적으로 생성되었습니다.');
    }
}
