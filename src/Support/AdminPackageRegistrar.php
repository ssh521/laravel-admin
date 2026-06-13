<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Support\Collection;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class AdminPackageRegistrar
{
    public function __construct(
        private readonly AdminMenuRegistrar $menus,
        private readonly AdminPermissionRegistrar $permissions,
    ) {}

    /**
     * @param  array{
     *     category?: array<string, mixed>,
     *     menus?: iterable<int, array<string, mixed>>,
     *     permissions?: iterable<int, string|array<string, mixed>>,
     *     roles?: iterable<int, string>,
     *     permission_roles?: iterable<int, string>
     * }  $definition
     * @return array{
     *     package_key: string,
     *     category: MenuCategory|null,
     *     menus: Collection<int, mixed>,
     *     permissions: Collection<int, mixed>,
     *     missing_menu_roles: array<int, string>,
     *     missing_permission_roles: array<int, string>
     * }
     */
    public function register(string $packageKey, array $definition): array
    {
        $roles = collect($definition['roles'] ?? $this->defaultRoleNames())->unique()->values();
        $permissionRoles = collect($definition['permission_roles'] ?? $roles)->unique()->values();
        $registeredPermissions = $this->permissions->register($definition['permissions'] ?? []);
        $category = null;
        $registeredMenus = collect();
        $missingMenuRoles = [];

        if (isset($definition['category'])) {
            $category = $this->menus->registerCategory($definition['category']);
            $registeredMenus = $this->menus->registerMenus($category, $definition['menus'] ?? []);
            $this->menus->refreshMenuCount($category);
            $missingMenuRoles = $this->menus->attachRoles($category, $roles);
        }

        $missingPermissionRoles = $this->permissions->attachToRoles($registeredPermissions, $permissionRoles);

        return [
            'package_key' => $packageKey,
            'category' => $category,
            'menus' => $registeredMenus,
            'permissions' => $registeredPermissions,
            'missing_menu_roles' => $missingMenuRoles,
            'missing_permission_roles' => $missingPermissionRoles,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function defaultRoleNames(): array
    {
        return [
            config('laravel-admin.super_admin_role', 'Super Admin'),
            'Admin',
        ];
    }
}
