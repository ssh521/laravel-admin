<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Support\AdminPackageRegistrar;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminPackageRegistrarTest extends TestCase
{
    public function test_it_registers_package_menus_permissions_and_default_role_links(): void
    {
        $result = app(AdminPackageRegistrar::class)->register('sample-package', [
            'category' => [
                'name' => '샘플 관리',
                'sort_order' => 99,
            ],
            'menus' => [
                [
                    'name' => '샘플 목록',
                    'route_name' => 'sample.admin.index',
                    'url' => '/admin/sample',
                    'sort_order' => 1,
                ],
            ],
            'permissions' => [
                ['name' => 'sample-view', 'description' => '샘플 조회', 'sort_order' => 990],
                ['name' => 'sample-manage', 'description' => '샘플 관리', 'sort_order' => 991],
            ],
        ]);

        $this->assertSame('sample-package', $result['package_key']);
        $this->assertSame([], $result['missing_menu_roles']);
        $this->assertSame([], $result['missing_permission_roles']);
        $this->assertCount(1, $result['menus']);
        $this->assertCount(2, $result['permissions']);

        $category = MenuCategory::query()->where('name', '샘플 관리')->firstOrFail();
        $menu = Menu::query()->where('route_name', 'sample.admin.index')->firstOrFail();
        $permission = Permission::query()->where('name', 'sample-manage')->firstOrFail();
        $adminRole = Role::query()->where('name', 'Admin')->where('guard_name', 'laravel_admin')->firstOrFail();
        $superAdminRole = Role::query()->where('name', 'Super Admin')->where('guard_name', 'laravel_admin')->firstOrFail();

        $this->assertSame($category->id, $menu->category_id);
        $this->assertSame(1, $category->fresh()->menu_count);
        $this->assertTrue($category->roles()->whereKey($adminRole->id)->exists());
        $this->assertTrue($category->roles()->whereKey($superAdminRole->id)->exists());
        $this->assertTrue($adminRole->permissions()->whereKey($permission->id)->exists());
        $this->assertTrue($superAdminRole->permissions()->whereKey($permission->id)->exists());
    }

    public function test_it_updates_existing_package_records_without_duplicates(): void
    {
        $registrar = app(AdminPackageRegistrar::class);

        $registrar->register('sample-package', [
            'category' => ['name' => '샘플 관리', 'sort_order' => 99],
            'menus' => [
                ['name' => '샘플 목록', 'route_name' => 'sample.admin.index', 'url' => '/admin/sample'],
            ],
            'permissions' => [
                ['name' => 'sample-manage', 'description' => '샘플 관리', 'sort_order' => 991],
            ],
        ]);

        $registrar->register('sample-package', [
            'category' => ['name' => '샘플 관리', 'sort_order' => 100],
            'menus' => [
                ['name' => '샘플 목록 변경', 'route_name' => 'sample.admin.index', 'url' => '/admin/sample'],
            ],
            'permissions' => [
                ['name' => 'sample-manage', 'description' => '샘플 관리 변경', 'sort_order' => 992],
            ],
        ]);

        $this->assertSame(1, MenuCategory::query()->where('name', '샘플 관리')->count());
        $this->assertSame(1, Menu::query()->where('route_name', 'sample.admin.index')->count());
        $this->assertSame(1, Permission::query()->where('name', 'sample-manage')->count());
        $this->assertSame(100, MenuCategory::query()->where('name', '샘플 관리')->value('sort_order'));
        $this->assertSame('샘플 목록 변경', Menu::query()->where('route_name', 'sample.admin.index')->value('name'));
        $this->assertSame('샘플 관리 변경', Permission::query()->where('name', 'sample-manage')->value('description'));
    }

    public function test_it_keeps_menu_roles_and_permission_roles_separate(): void
    {
        Role::query()->firstOrCreate([
            'name' => 'Editor',
            'guard_name' => 'laravel_admin',
        ]);

        app(AdminPackageRegistrar::class)->register('sample-package', [
            'category' => [
                'name' => '에디터 관리',
                'sort_order' => 98,
            ],
            'menus' => [
                [
                    'name' => '에디터 목록',
                    'route_name' => 'sample.editor.index',
                    'url' => '/admin/sample/editors',
                ],
            ],
            'permissions' => [
                ['name' => 'sample-editor-manage'],
            ],
            'roles' => ['Editor'],
            'permission_roles' => ['Super Admin'],
        ]);

        $category = MenuCategory::query()->where('name', '에디터 관리')->firstOrFail();
        $permission = Permission::query()->where('name', 'sample-editor-manage')->firstOrFail();
        $editorRole = Role::query()->where('name', 'Editor')->where('guard_name', 'laravel_admin')->firstOrFail();
        $adminRole = Role::query()->where('name', 'Admin')->where('guard_name', 'laravel_admin')->firstOrFail();
        $superAdminRole = Role::query()->where('name', 'Super Admin')->where('guard_name', 'laravel_admin')->firstOrFail();

        $this->assertTrue($category->roles()->whereKey($editorRole->id)->exists());
        $this->assertFalse($category->roles()->whereKey($adminRole->id)->exists());
        $this->assertFalse($category->roles()->whereKey($superAdminRole->id)->exists());
        $this->assertTrue($superAdminRole->permissions()->whereKey($permission->id)->exists());
        $this->assertFalse($editorRole->permissions()->whereKey($permission->id)->exists());
        $this->assertFalse($adminRole->permissions()->whereKey($permission->id)->exists());
    }
}
