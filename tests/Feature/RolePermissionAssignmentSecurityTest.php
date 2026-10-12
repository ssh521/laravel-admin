<?php

use Composer\Semver\Semver;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Gate;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Ssh521\LaravelAdmin\Livewire\Admin\Roles\RoleEditModal;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Services\RoleService;
use Ssh521\LaravelAdmin\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutVite();
    config()->set('laravel-admin.broadcast_notifications.enabled', false);
});

function rolePatchAdmin(array $permissions = [], ?Role $role = null): AdminUser
{
    $admin = (new class extends Factory
    {
        protected $model = AdminUser::class;

        public function definition(): array
        {
            return ['name' => fake()->name(), 'email' => fake()->unique()->safeEmail(), 'password' => 'test-password'];
        }
    })->create();

    $admin->givePermissionTo(Permission::query()->where('guard_name', 'laravel_admin')->whereIn('name', $permissions)->get());

    if ($role !== null) {
        $admin->assignRole($role);
    }

    return $admin;
}

function rolePatchRole(array $permissions = [], ?string $name = null): Role
{
    $role = (new class extends Factory
    {
        protected $model = Role::class;

        public function definition(): array
        {
            return ['name' => fake()->unique()->uuid(), 'guard_name' => 'laravel_admin'];
        }
    })->state($name === null ? [] : ['name' => $name])->create();

    $role->syncPermissions(Permission::query()->where('guard_name', 'laravel_admin')->whereIn('name', $permissions)->get());

    return $role;
}

function rolePatchPermission(string $name): int
{
    return Permission::query()->where('guard_name', 'laravel_admin')->where('name', $name)->firstOrFail()->id;
}

test('role edit alone cannot grant a protected permission through either entry point', function (string $entry): void {
    $role = rolePatchRole();
    $admin = rolePatchAdmin(['role-edit'], $role);
    $permissionId = rolePatchPermission('super-admin-manage');
    expect(Gate::forUser($admin)->allows('assignPermissions', $role))->toBeFalse();

    if ($entry === 'http') {
        $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
            'name' => 'Denied rename', 'permissions' => [$permissionId],
        ])->assertForbidden();
    } else {
        Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id])
            ->set('name', 'Denied rename')->set('permissionIds', [$permissionId])->call('save')
            ->assertForbidden()->assertNotDispatched('admin-roles:role:saved');
    }

    expect($role->refresh()->name)->not->toBe('Denied rename');
    expect($role->permissions()->count())->toBe(0);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    expect($admin->fresh()->hasPermissionTo('super-admin-manage'))->toBeFalse();
})->with(['http', 'livewire']);

test('role creation cannot assign permissions without both assignment capabilities', function (array $abilities): void {
    $admin = rolePatchAdmin($abilities);
    $this->actingAs($admin, 'laravel_admin')->post(route('admin.roles.store'), [
        'name' => 'Denied creation', 'permissions' => [rolePatchPermission('user-list')],
    ])->assertForbidden();
    $this->assertDatabaseMissing('roles', ['name' => 'Denied creation']);
})->with([
    'create only' => [['role-create']],
    'missing per-permission grant' => [['role-create', 'role-assign-permissions']],
    'missing role assignment' => [['role-create', 'permission-assign-to-roles']],
]);

test('permission changes require both assignment capabilities', function (array $abilities): void {
    $role = rolePatchRole();
    $admin = rolePatchAdmin($abilities);
    $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
        'name' => $role->name, 'permissions' => [rolePatchPermission('user-list')],
    ])->assertForbidden();
    expect($role->permissions()->count())->toBe(0);
})->with([
    'missing per-permission grant' => [['role-edit', 'role-assign-permissions']],
    'missing role assignment' => [['role-edit', 'permission-assign-to-roles']],
]);

test('authorized grantors can create change and clear ordinary permissions', function (): void {
    $admin = rolePatchAdmin(['role-create', 'role-edit', 'role-assign-permissions', 'permission-assign-to-roles']);
    $this->actingAs($admin, 'laravel_admin')->post(route('admin.roles.store'), [
        'name' => 'Allowed creation', 'permissions' => [rolePatchPermission('user-list')],
    ])->assertRedirect();
    $role = Role::query()->where('name', 'Allowed creation')->firstOrFail();
    expect($role->hasPermissionTo('user-list'))->toBeTrue();

    Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id])
        ->set('permissionIds', [rolePatchPermission('menu-list')])->call('save')->assertDispatched('admin-roles:role:saved');
    expect($role->fresh()->hasPermissionTo('menu-list'))->toBeTrue();
    expect($role->fresh()->hasPermissionTo('user-list'))->toBeFalse();

    $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
        'name' => $role->name, 'permissions_present' => true,
    ])->assertRedirect();
    expect($role->permissions()->count())->toBe(0);
});

test('non super admins cannot add or remove protected permissions', function (string $operation, string $entry): void {
    $role = rolePatchRole($operation === 'remove' ? ['super-admin-manage'] : []);
    $admin = rolePatchAdmin(['role-edit', 'role-assign-permissions', 'permission-assign-to-roles']);
    $ids = $operation === 'remove' ? [] : [rolePatchPermission('super-admin-manage')];

    if ($entry === 'http') {
        $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
            'name' => 'Denied protected change', 'permissions' => $ids,
        ])->assertForbidden();
    } else {
        Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id])
            ->set('name', 'Denied protected change')->set('permissionIds', $ids)->call('save')->assertForbidden();
    }

    expect($role->fresh()->name)->not->toBe('Denied protected change');
    expect($role->permissions()->count())->toBe($operation === 'remove' ? 1 : 0);
})->with(['add', 'remove'])->with(['http', 'livewire']);

test('edit only actors can preserve permissions while editing metadata', function (string $entry): void {
    $role = rolePatchRole(['user-list', 'menu-list']);
    $admin = rolePatchAdmin(['role-edit']);
    $ids = $role->permissions->pluck('id')->all();

    if ($entry === 'http') {
        $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
            'name' => 'Metadata only', 'permissions' => array_merge(array_reverse(array_map('strval', $ids)), [$ids[0]]),
        ])->assertRedirect();
    } else {
        Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id])
            ->set('name', 'Metadata only')->call('save')->assertDispatched('admin-roles:role:saved');
    }

    expect($role->fresh()->name)->toBe('Metadata only');
    expect($role->permissions()->count())->toBe(2);
})->with(['http', 'livewire']);

test('omitted permission input preserves grants and explicit empty input requires authorization', function (): void {
    $role = rolePatchRole(['user-list']);
    $admin = rolePatchAdmin(['role-edit']);
    $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), ['name' => 'Omitted permissions'])->assertRedirect();
    expect($role->permissions()->count())->toBe(1);
    $this->put(route('admin.roles.update', $role), ['name' => 'Denied clearing', 'permissions' => []])->assertForbidden();
    expect($role->fresh()->name)->toBe('Omitted permissions');
    expect($role->permissions()->count())->toBe(1);
});

test('default Admin keeps unchanged protected grants while changing ordinary permissions', function (): void {
    $defaultRole = Role::query()->where('name', 'Admin')->where('guard_name', 'laravel_admin')->firstOrFail();
    $admin = rolePatchAdmin([], $defaultRole);
    $role = rolePatchRole(['super-admin-manage', 'user-list']);
    $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
        'name' => 'Admin compatible', 'permissions' => [rolePatchPermission('super-admin-manage'), rolePatchPermission('menu-list')],
    ])->assertRedirect();
    expect($role->fresh()->hasPermissionTo('super-admin-manage'))->toBeTrue();
    expect($role->fresh()->hasPermissionTo('menu-list'))->toBeTrue();
    expect($role->fresh()->hasPermissionTo('user-list'))->toBeFalse();
});

test('configured Super Admin alias cannot be claimed by delegated role writers', function (string $entry): void {
    config()->set('laravel-admin.super_admin_role', 'Absent Root Administrator');
    $role = rolePatchRole();
    $admin = rolePatchAdmin(['role-create', 'role-edit', 'role-assign-permissions', 'permission-assign-to-roles'], $role);

    if ($entry === 'create') {
        $this->actingAs($admin, 'laravel_admin')->post(route('admin.roles.store'), ['name' => 'Absent Root Administrator'])->assertForbidden();
    } elseif ($entry === 'http') {
        $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), ['name' => 'Absent Root Administrator', 'permissions' => []])->assertForbidden();
    } else {
        Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id])
            ->set('name', 'Absent Root Administrator')->call('save')->assertForbidden();
    }

    $this->assertDatabaseMissing('roles', ['name' => 'Absent Root Administrator']);
    expect(Gate::forUser($admin->fresh())->allows('manageUsers'))->toBeFalse();
})->with(['create', 'http', 'livewire']);

test('shared service refuses forged permission changes before any mutation', function (): void {
    $role = rolePatchRole();
    $admin = rolePatchAdmin(['role-edit']);
    $this->actingAs($admin, 'laravel_admin');
    expect(fn () => app(RoleService::class)->updateRole([
        'roleId' => $role->id, 'name' => 'Denied service rename', 'permissionIds' => [rolePatchPermission('super-admin-manage')],
    ]))->toThrow(AuthorizationException::class);
    expect($role->fresh()->name)->not->toBe('Denied service rename');
    expect($role->permissions()->count())->toBe(0);
});

test('Super Admin role names use literal comparisons rather than role expression syntax', function (string $configuredName, string $ordinaryName): void {
    config()->set('laravel-admin.super_admin_role', $configuredName);
    $role = rolePatchRole();
    $admin = rolePatchAdmin(['role-edit'], $role);
    $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), ['name' => $ordinaryName])->assertRedirect();
    expect(Gate::forUser($admin->fresh())->allows('manageUsers'))->toBeFalse();

    $rootRole = rolePatchRole([], $configuredName);
    $root = rolePatchAdmin([], $rootRole);
    expect(Gate::forUser($root)->allows('manageUsers'))->toBeTrue();
})->with([
    'role expression' => ['Root|Administrator', 'Root'],
    'numeric role string' => ['01', '1'],
]);

test('a Super Admin name in another guard does not bypass administrator policies', function (): void {
    $role = rolePatchRole();
    $role->update(['name' => 'Super Admin', 'guard_name' => 'web']);
    $admin = rolePatchAdmin();
    $admin->roles()->attach($role);
    expect(Gate::forUser($admin->fresh())->allows('manageUsers'))->toBeFalse();
});

test('read only modals preserve refreshed permissions during metadata edits', function (): void {
    $role = rolePatchRole(['user-list']);
    $admin = rolePatchAdmin(['role-edit']);
    $modal = Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id]);
    $role->syncPermissions([rolePatchPermission('menu-list')]);
    $modal->set('name', 'Fresh metadata')->call('save')->assertDispatched('admin-roles:role:saved');
    expect($role->fresh()->name)->toBe('Fresh metadata');
    expect($role->fresh()->hasPermissionTo('menu-list'))->toBeTrue();
    expect($role->fresh()->hasPermissionTo('user-list'))->toBeFalse();
});

test('clients cannot replace the original permission snapshot', function (): void {
    $role = rolePatchRole();
    $admin = rolePatchAdmin(['role-edit']);
    expect(fn () => Livewire::actingAs($admin, 'laravel_admin')->test(RoleEditModal::class, ['roleId' => $role->id])
        ->set('originalPermissionIds', [rolePatchPermission('super-admin-manage')]))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

test('Super Admin with no direct grants retains protected assignment authority', function (): void {
    $superRole = Role::query()->where('name', 'Super Admin')->where('guard_name', 'laravel_admin')->firstOrFail();
    $superRole->syncPermissions([]);
    $admin = rolePatchAdmin([], $superRole);
    $role = rolePatchRole();
    $this->actingAs($admin, 'laravel_admin')->put(route('admin.roles.update', $role), [
        'name' => 'Super Admin allowed', 'permissions' => [rolePatchPermission('super-admin-manage')],
    ])->assertRedirect();
    expect($role->fresh()->hasPermissionTo('super-admin-manage'))->toBeTrue();
});

test('core dependency requires the UI role permission security patch', function (string $uiVersion, bool $isCompatible): void {
    $composerManifest = json_decode(file_get_contents($this->packagePath('composer.json')), true, flags: JSON_THROW_ON_ERROR);
    $adminUiVersionConstraint = $composerManifest['require']['ssh521/laravel-admin-ui'];

    expect(Semver::satisfies($uiVersion, $adminUiVersionConstraint))->toBe($isCompatible);
})->with([
    'old UI is excluded' => ['1.0.4', false],
    'patched UI is accepted' => ['1.0.5', true],
    'compatible UI is accepted' => ['1.1.0', true],
    'incompatible major is excluded' => ['2.0.0', false],
]);
