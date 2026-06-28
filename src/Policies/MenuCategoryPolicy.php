<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('menu-category-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, MenuCategory $menuCategory): bool
    {
        if ($user->hasPermissionTo('menu-category-view')) {
            return true;
        }

        if ($menuCategory->roles->isEmpty()) {
            return true;
        }

        return $user->roles->intersect($menuCategory->roles)->isNotEmpty();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return $user->hasPermissionTo('menu-category-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, MenuCategory $menuCategory): bool
    {
        return $user->hasPermissionTo('menu-category-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, MenuCategory $menuCategory): bool
    {
        return $user->hasPermissionTo('menu-category-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, MenuCategory $menuCategory): bool
    {
        return $user->hasPermissionTo('menu-category-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, MenuCategory $menuCategory): bool
    {
        return $user->hasPermissionTo('menu-category-delete');
    }
}
