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
        // Super Admin은 모든 메뉴 카테고리를 볼 수 있음
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin은 메뉴 카테고리 목록을 볼 수 있음
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 특정 권한을 가진 사용자도 볼 수 있음
        return $user->hasPermissionTo('menu-category-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, MenuCategory $menuCategory): bool
    {
        // Super Admin은 모든 메뉴 카테고리를 볼 수 있음
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin은 모든 메뉴 카테고리를 볼 수 있음
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 사용자가 해당 카테고리에 접근 권한이 있는지 확인
        if ($menuCategory->roles->isEmpty()) {
            // 역할 제한이 없는 경우 모든 사용자가 볼 수 있음
            return true;
        }

        // 사용자의 역할이 카테고리의 허용 역할에 포함되어 있는지 확인
        return $user->roles->intersect($menuCategory->roles)->isNotEmpty();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        // Super Admin은 메뉴 카테고리를 생성할 수 있음
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin은 메뉴 카테고리를 생성할 수 있음
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 특정 권한을 가진 사용자도 생성할 수 있음
        return $user->hasPermissionTo('menu-category-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, MenuCategory $menuCategory): bool
    {
        // Super Admin은 모든 메뉴 카테고리를 수정할 수 있음
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin은 모든 메뉴 카테고리를 수정할 수 있음
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 특정 권한을 가진 사용자도 수정할 수 있음
        return $user->hasPermissionTo('menu-category-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, MenuCategory $menuCategory): bool
    {
        // Super Admin은 모든 메뉴 카테고리를 삭제할 수 있음
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin은 모든 메뉴 카테고리를 삭제할 수 있음
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 특정 권한을 가진 사용자도 삭제할 수 있음
        return $user->hasPermissionTo('menu-category-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, MenuCategory $menuCategory): bool
    {
        // Super Admin은 모든 메뉴 카테고리를 복원할 수 있음
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin은 모든 메뉴 카테고리를 복원할 수 있음
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 특정 권한을 가진 사용자도 복원할 수 있음
        return $user->hasPermissionTo('menu-category-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, MenuCategory $menuCategory): bool
    {
        // Super Admin만 영구 삭제 가능
        return $user->hasRole('Super Admin');
    }
}
