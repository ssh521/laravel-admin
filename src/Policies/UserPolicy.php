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
        if (! $this->tokenAllows($user, 'read')) {
            return false;
        }

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
        if (! $this->tokenAllows($user, 'read')) {
            return false;
        }

        if ($this->isSameAccount($user, $model)) {
            return true;
        }

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
        if (! $this->tokenAllows($user, 'create')) {
            return false;
        }

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
        if (! $this->tokenAllows($user, 'update')) {
            return false;
        }

        if ($this->isSameAccount($user, $model)) {
            return true;
        }

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
        if (! $this->tokenAllows($user, 'delete')) {
            return false;
        }

        if ($this->isSameAccount($user, $model)) {
            return false;
        }

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
        if (! $this->tokenAllows($user, 'update')) {
            return false;
        }

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
        if (! $this->tokenAllows($user, 'delete')) {
            return false;
        }

        if ($this->isSameAccount($user, $model)) {
            return false;
        }

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
        if (! $this->tokenAllows($user, 'read')) {
            return false;
        }

        if (! $this->isAdminActor($user)) {
            return false;
        }

        return $user->hasPermissionTo('user-view-auth-list');
    }

    private function isAdminActor($user): bool
    {
        if (! method_exists($user, 'hasPermissionTo')) {
            return false;
        }

        $adminUserModel = config('laravel-admin.admin_user_model');

        if (is_string($adminUserModel) && $user instanceof $adminUserModel) {
            return true;
        }

        return method_exists($user, 'currentAccessToken')
            && $user->currentAccessToken() !== null;
    }

    private function tokenAllows($user, string $ability): bool
    {
        if (! method_exists($user, 'currentAccessToken') || $user->currentAccessToken() === null) {
            return true;
        }

        return method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    private function isSameAccount($user, $model): bool
    {
        if (! method_exists($user, 'getTable') || ! method_exists($model, 'getTable')) {
            return isset($user->id, $model->id) && $user->id === $model->id;
        }

        return $user->getTable() === $model->getTable()
            && $user->getKey() === $model->getKey();
    }
}
