<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Livewire\Admin\MenuCategories\MenuCategoryRolesModal;
use Ssh521\LaravelAdmin\Livewire\Admin\Roles\RoleEditModal;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminGuardIsolationTest extends TestCase
{
    public function test_generic_permission_and_role_pages_only_show_admin_guard_records(): void
    {
        [$admin, $guard] = $this->createSuperAdmin();
        $adminPermission = Permission::query()->create([
            'name' => 'human-admin-visible',
            'guard_name' => $guard,
            'description' => 'Human admin marker',
        ]);
        $automationPermission = Permission::query()->create([
            'name' => 'automation-hidden',
            'guard_name' => 'laravel_automation',
            'description' => 'Automation hidden marker',
        ]);
        $adminRole = Role::query()->create([
            'name' => 'Human Operator',
            'guard_name' => $guard,
        ]);
        $automationRole = Role::query()->create([
            'name' => 'Automation Runner',
            'guard_name' => 'laravel_automation',
        ]);

        $permissionResponse = $this->actingAs($admin, $guard)
            ->get(route('admin.permissions.index'));

        $permissionResponse->assertOk();
        $this->assertTrue($permissionResponse->viewData('data')->contains('id', $adminPermission->id));
        $this->assertFalse($permissionResponse->viewData('data')->contains('id', $automationPermission->id));

        $searchResponse = $this->actingAs($admin, $guard)
            ->get(route('admin.permissions.index', ['search' => 'Automation hidden marker']));

        $searchResponse->assertOk();
        $this->assertFalse($searchResponse->viewData('data')->contains('id', $automationPermission->id));

        $roleResponse = $this->actingAs($admin, $guard)
            ->get(route('admin.roles.index'));

        $roleResponse->assertOk();
        $this->assertTrue($roleResponse->viewData('data')->contains('id', $adminRole->id));
        $this->assertFalse($roleResponse->viewData('data')->contains('id', $automationRole->id));

        $roleCreateResponse = $this->actingAs($admin, $guard)
            ->get(route('admin.roles.create'));

        $roleCreateResponse->assertOk();
        $this->assertTrue($roleCreateResponse->viewData('permissions')->contains('id', $adminPermission->id));
        $this->assertFalse($roleCreateResponse->viewData('permissions')->contains('id', $automationPermission->id));

        $dashboardResponse = $this->actingAs($admin, $guard)
            ->get(route('admin.index'));

        $dashboardResponse->assertOk();
        $this->assertSame(
            Role::query()->where('guard_name', $guard)->count(),
            $dashboardResponse->viewData('stats')['total_roles']
        );
        $this->assertSame(
            Permission::query()->where('guard_name', $guard)->count(),
            $dashboardResponse->viewData('stats')['total_permissions']
        );
    }

    public function test_generic_routes_cannot_read_or_mutate_automation_guard_records(): void
    {
        [$admin, $guard] = $this->createSuperAdmin();
        $automationPermission = Permission::query()->create([
            'name' => 'automation-protected',
            'guard_name' => 'laravel_automation',
        ]);
        $automationRole = Role::query()->create([
            'name' => 'Automation Protected',
            'guard_name' => 'laravel_automation',
        ]);

        foreach (['show', 'edit'] as $route) {
            $this->actingAs($admin, $guard)
                ->get(route("admin.permissions.{$route}", $automationPermission))
                ->assertNotFound();

            $this->actingAs($admin, $guard)
                ->get(route("admin.roles.{$route}", $automationRole))
                ->assertNotFound();
        }

        $this->actingAs($admin, $guard)
            ->put(route('admin.permissions.update', $automationPermission), [
                'name' => 'automation-changed',
            ])
            ->assertNotFound();
        $this->actingAs($admin, $guard)
            ->delete(route('admin.permissions.destroy', $automationPermission))
            ->assertNotFound();
        $this->actingAs($admin, $guard)
            ->put(route('admin.roles.update', $automationRole), [
                'name' => 'Automation Changed',
            ])
            ->assertNotFound();
        $this->actingAs($admin, $guard)
            ->delete(route('admin.roles.destroy', $automationRole))
            ->assertNotFound();

        $this->assertDatabaseHas('permissions', [
            'id' => $automationPermission->id,
            'name' => 'automation-protected',
            'guard_name' => 'laravel_automation',
        ]);
        $this->assertDatabaseHas('roles', [
            'id' => $automationRole->id,
            'name' => 'Automation Protected',
            'guard_name' => 'laravel_automation',
        ]);
    }

    public function test_admin_mutations_reject_automation_guard_ids_and_scope_uniqueness_by_guard(): void
    {
        [$admin, $guard] = $this->createSuperAdmin();
        $automationPermission = Permission::query()->create([
            'name' => 'shared-permission-name',
            'guard_name' => 'laravel_automation',
        ]);
        $automationRole = Role::query()->create([
            'name' => 'Shared Role Name',
            'guard_name' => 'laravel_automation',
        ]);
        $category = MenuCategory::query()->create([
            'name' => 'Guard-isolated category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin, $guard)
            ->post(route('admin.permissions.store'), [
                'name' => $automationPermission->name,
            ])
            ->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', [
            'name' => $automationPermission->name,
            'guard_name' => $guard,
        ]);

        $this->actingAs($admin, $guard)
            ->post(route('admin.roles.store'), [
                'name' => $automationRole->name,
            ])
            ->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', [
            'name' => $automationRole->name,
            'guard_name' => $guard,
        ]);

        $this->actingAs($admin, $guard)
            ->postJson(route('admin.permissions.update-order'), [
                'order' => [
                    ['id' => $automationPermission->id, 'order' => 1],
                ],
            ])
            ->assertUnprocessable();

        $this->actingAs($admin, $guard)
            ->postJson(route('admin.roles.store'), [
                'name' => 'Invalid Automation Permission Role',
                'permissions' => [$automationPermission->id],
            ])
            ->assertUnprocessable();

        $this->actingAs($admin, $guard)
            ->postJson(route('admin.menu-categories.update-roles-api', $category), [
                'roles' => [$automationRole->id],
            ])
            ->assertUnprocessable();
    }

    public function test_livewire_role_selectors_hide_automation_guard_records(): void
    {
        [$admin, $guard] = $this->createSuperAdmin();
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', $guard)
            ->firstOrFail();
        $automationPermission = Permission::query()->create([
            'name' => 'automation-modal-hidden',
            'guard_name' => 'laravel_automation',
        ]);
        $automationRole = Role::query()->create([
            'name' => 'Automation Modal Hidden',
            'guard_name' => 'laravel_automation',
        ]);
        $category = MenuCategory::query()->create([
            'name' => 'Modal guard category',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $category->roles()->attach($automationRole);

        $roleModal = Livewire::actingAs($admin, $guard)
            ->test(RoleEditModal::class, ['roleId' => $adminRole->id]);
        $menuRoleModal = Livewire::actingAs($admin, $guard)
            ->test(MenuCategoryRolesModal::class, ['menuCategoryId' => $category->id]);

        $this->assertNotContains(
            $automationPermission->id,
            collect($roleModal->get('availablePermissions'))->pluck('id')->all()
        );
        $this->assertNotContains(
            $automationRole->id,
            collect($menuRoleModal->get('availableRoles'))->pluck('id')->all()
        );
        $this->assertNotContains((string) $automationRole->id, $menuRoleModal->get('roleIds'));
    }

    /**
     * @return array{0: AdminUser, 1: string}
     */
    private function createSuperAdmin(): array
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $role = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();
        $admin = AdminUser::query()->forceCreate([
            'name' => 'Guard Isolation Admin',
            'email' => 'guard-isolation@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($role);

        return [$admin, $guard];
    }
}
