<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class AdminMenuRegistrar
{
    /**
     * @param  array{name: string, sort_order?: int, is_active?: bool}  $categoryData
     */
    public function registerCategory(array $categoryData): MenuCategory
    {
        return MenuCategory::query()->updateOrCreate(
            ['name' => $categoryData['name']],
            [
                'is_active' => $categoryData['is_active'] ?? true,
                'sort_order' => $categoryData['sort_order'] ?? 0,
            ]
        );
    }

    /**
     * @param  iterable<int, array<string, mixed>>  $menus
     * @return Collection<int, Menu>
     */
    public function registerMenus(MenuCategory $category, iterable $menus): Collection
    {
        return collect($menus)
            ->map(fn (array $menuData): Menu => $this->registerMenu($category, $menuData))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $menuData
     */
    public function registerMenu(MenuCategory $category, array $menuData): Menu
    {
        $attributes = $this->uniqueMenuAttributes($category, $menuData);
        $values = array_merge([
            'target' => '_self',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 0,
        ], $menuData, [
            'category_id' => $category->id,
        ]);

        unset($values['roles'], $values['permissions']);

        return Menu::query()->updateOrCreate($attributes, $values);
    }

    /**
     * @param  iterable<int, string>  $roleNames
     * @return array<int, string>
     */
    public function attachRoles(MenuCategory $category, iterable $roleNames): array
    {
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');
        $roleNames = collect($roleNames)->filter()->unique()->values();

        if ($roleNames->isEmpty()) {
            return [];
        }

        $roles = Role::query()
            ->where('guard_name', $guardName)
            ->whereIn('name', $roleNames)
            ->get(['id', 'name']);

        if ($roles->isNotEmpty()) {
            $category->roles()->syncWithoutDetaching($roles->pluck('id')->all());
        }

        return $roleNames
            ->diff($roles->pluck('name'))
            ->values()
            ->all();
    }

    public function refreshMenuCount(MenuCategory $category): void
    {
        $category->update([
            'menu_count' => Menu::query()->where('category_id', $category->id)->count(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $menuData
     * @return array<string, mixed>
     */
    private function uniqueMenuAttributes(MenuCategory $category, array $menuData): array
    {
        if (! empty($menuData['route_name'])) {
            return ['route_name' => $menuData['route_name']];
        }

        if (! empty($menuData['url'])) {
            return [
                'category_id' => $category->id,
                'url' => $menuData['url'],
            ];
        }

        return [
            'category_id' => $category->id,
            'name' => $menuData['name'],
        ];
    }
}
