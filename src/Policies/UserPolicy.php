<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, $model): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, $model): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, $model): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, $model): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, $model): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-delete');
    }

    /**
     * Determine whether the user can view user authentication list.
     */
    public function viewAuthList($user): bool
    {
        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-view-auth-list');
    }

    private function isAdminActor($user): bool
    {
        $adminUserModel = config('laravel-admin.admin_user_model');

        return is_string($adminUserModel)
            && $user instanceof $adminUserModel
            && method_exists($user, 'hasPermissionTo');
    }
}
