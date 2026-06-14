<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar as SpatiePermissionRegistrar;
use Ssh521\LaravelAdmin\Models\Permission;

class AdminPermissionRegistrar
{
    /**
     * @param  iterable<int, string|array<string, mixed>>  $permissions
     * @return Collection<int, Permission>
     */
    public function register(iterable $permissions): Collection
    {
        return collect($permissions)
            ->map(fn (string|array $permission): Permission => $this->registerOne($permission))
            ->values();
    }

    /**
     * @param  iterable<int, Permission>  $permissions
     * @param  iterable<int, string>  $roleNames
     * @return array<int, string>
     */
    public function attachToRoles(iterable $permissions, iterable $roleNames): array
    {
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');
        $roleNames = collect($roleNames)->filter()->unique()->values();
        $permissions = collect($permissions);

        if ($roleNames->isEmpty() || $permissions->isEmpty()) {
            return [];
        }

        $roles = Role::query()
            ->where('guard_name', $guardName)
            ->whereIn('name', $roleNames)
            ->get(['id', 'name']);

        $permissionIds = $permissions->pluck('id')->all();

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }

        app(SpatiePermissionRegistrar::class)->forgetCachedPermissions();

        return $roleNames
            ->diff($roles->pluck('name'))
            ->values()
            ->all();
    }

    /**
     * @param  string|array<string, mixed>  $permission
     */
    private function registerOne(string|array $permission): Permission
    {
        $data = is_string($permission) ? ['name' => $permission] : $permission;
        $guardName = $data['guard_name'] ?? config('laravel-admin.auth.guard', 'laravel_admin');

        return Permission::query()->updateOrCreate(
            [
                'name' => $data['name'],
                'guard_name' => $guardName,
            ],
            array_filter([
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? null,
            ], fn ($value): bool => $value !== null)
        );
    }
}
