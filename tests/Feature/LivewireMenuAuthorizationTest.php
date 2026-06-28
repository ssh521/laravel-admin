<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Livewire\Admin\Menus\ChangeCategoryModal;
use Ssh521\LaravelAdmin\Livewire\Admin\Menus\MenuOrderModal;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Tests\TestCase;

class LivewireMenuAuthorizationTest extends TestCase
{
    public function test_admin_without_menu_edit_permission_cannot_change_menu_categories(): void
    {
        [$admin, $guard] = $this->createAdminWithoutPermissions();
        [$menu, $targetCategory, $sourceCategory] = $this->createMenuFixture();

        Livewire::actingAs($admin, $guard)
            ->test(ChangeCategoryModal::class)
            ->set('selectedMenuIds', [$menu->id])
            ->set('categoryId', $targetCategory->id)
            ->call('save')
            ->assertForbidden();

        $this->assertSame($sourceCategory->id, $menu->refresh()->category_id);
    }

    public function test_admin_without_menu_edit_permission_can_select_menus_without_writing(): void
    {
        [$admin, $guard] = $this->createAdminWithoutPermissions();
        [$menu] = $this->createMenuFixture();

        Livewire::actingAs($admin, $guard)
            ->test(ChangeCategoryModal::class)
            ->call('setSelectedMenuIds', ['ids' => [$menu->id]])
            ->assertSet('selectedMenuIds', [$menu->id]);
    }

    public function test_super_admin_can_change_menu_categories_without_direct_permission(): void
    {
        [$superAdmin, $guard] = $this->createSuperAdminWithoutPermissions();
        [$menu, $targetCategory] = $this->createMenuFixture();

        Livewire::actingAs($superAdmin, $guard)
            ->test(ChangeCategoryModal::class)
            ->set('selectedMenuIds', [$menu->id])
            ->set('categoryId', $targetCategory->id)
            ->call('save');

        $this->assertSame($targetCategory->id, $menu->refresh()->category_id);
    }

    public function test_admin_without_menu_edit_permission_cannot_reorder_menus(): void
    {
        [$admin, $guard] = $this->createAdminWithoutPermissions();
        [$menu, , $sourceCategory] = $this->createMenuFixture();

        Livewire::actingAs($admin, $guard)
            ->test(MenuOrderModal::class)
            ->set('categoryId', $sourceCategory->id)
            ->set('menus', [
                ['id' => $menu->id, 'name' => $menu->name, 'sort_order' => 0, 'icon' => null, 'is_active' => true],
            ])
            ->call('updateOrder', [
                ['id' => $menu->id, 'sort_order' => 3],
            ])
            ->assertForbidden();

        $this->assertSame(1, $menu->refresh()->sort_order);
    }

    public function test_super_admin_can_reorder_menus_without_direct_permission(): void
    {
        [$superAdmin, $guard] = $this->createSuperAdminWithoutPermissions();
        [$menu, , $sourceCategory] = $this->createMenuFixture();

        Livewire::actingAs($superAdmin, $guard)
            ->test(MenuOrderModal::class)
            ->set('categoryId', $sourceCategory->id)
            ->set('menus', [
                ['id' => $menu->id, 'name' => $menu->name, 'sort_order' => 0, 'icon' => null, 'is_active' => true],
            ])
            ->call('updateOrder', [
                ['id' => $menu->id, 'sort_order' => 3],
            ]);

        $this->assertSame(0, $menu->refresh()->sort_order);
    }

    public function test_super_admin_can_reorder_menus_from_livewire_event(): void
    {
        [$superAdmin, $guard] = $this->createSuperAdminWithoutPermissions();
        [$menu, , $sourceCategory] = $this->createMenuFixture();
        $secondMenu = Menu::query()->create([
            'name' => 'Second Managed Menu',
            'url' => '/admin/second-managed-menu',
            'category_id' => $sourceCategory->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Livewire::actingAs($superAdmin, $guard)
            ->test(MenuOrderModal::class)
            ->set('categoryId', $sourceCategory->id)
            ->set('menus', [
                ['id' => $menu->id, 'name' => $menu->name, 'sort_order' => 1, 'icon' => null, 'is_active' => true],
                ['id' => $secondMenu->id, 'name' => $secondMenu->name, 'sort_order' => 2, 'icon' => null, 'is_active' => true],
            ])
            ->call('updateOrderFromEvent', [
                'data' => [
                    'categoryId' => $sourceCategory->id,
                    'newOrder' => [
                        ['id' => $secondMenu->id, 'sort_order' => 0],
                        ['id' => $menu->id, 'sort_order' => 1],
                    ],
                ],
            ]);

        $this->assertSame(1, $menu->refresh()->sort_order);
        $this->assertSame(0, $secondMenu->refresh()->sort_order);
    }

    public function test_menu_edit_authorization_does_not_mutate_default_auth_guard(): void
    {
        [$superAdmin, $guard] = $this->createSuperAdminWithoutPermissions();
        [$menu, , $sourceCategory] = $this->createMenuFixture();

        Auth::shouldUse('web');

        Auth::guard($guard)->setUser($superAdmin);

        $component = new MenuOrderModal;
        $component->categoryId = $sourceCategory->id;
        $component->menus = [
            ['id' => $menu->id, 'name' => $menu->name, 'sort_order' => 0, 'icon' => null, 'is_active' => true],
        ];
        $component->updateOrder([
            ['id' => $menu->id, 'sort_order' => 3],
        ]);

        $this->assertSame('web', Auth::getDefaultDriver());
    }

    /**
     * @return array{0: AdminUser, 1: string}
     */
    private function createAdminWithoutPermissions(): array
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', $guard)
            ->firstOrFail();
        $adminRole->syncPermissions([]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = AdminUser::query()->forceCreate([
            'name' => 'Admin User',
            'email' => 'livewire-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($adminRole);

        return [$admin, $guard];
    }

    /**
     * @return array{0: AdminUser, 1: string}
     */
    private function createSuperAdminWithoutPermissions(): array
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();
        $superAdminRole->syncPermissions([]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = AdminUser::query()->forceCreate([
            'name' => 'Super Admin',
            'email' => 'livewire-super@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $superAdmin->assignRole($superAdminRole);

        return [$superAdmin, $guard];
    }

    /**
     * @return array{0: Menu, 1: MenuCategory, 2: MenuCategory}
     */
    private function createMenuFixture(): array
    {
        $sourceCategory = MenuCategory::query()->create([
            'name' => 'Source Category',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $targetCategory = MenuCategory::query()->create([
            'name' => 'Target Category',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $menu = Menu::query()->create([
            'name' => 'Managed Menu',
            'url' => '/admin/managed-menu',
            'category_id' => $sourceCategory->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return [$menu, $targetCategory, $sourceCategory];
    }
}
