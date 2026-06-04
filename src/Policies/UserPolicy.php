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
        // API 요청인 경우 토큰의 'read' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('read')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, $model): bool
    {
        // 사용자는 자신의 프로필을 볼 수 있음
        if ($this->isSameAccount($user, $model)) {
            return true;
        }

        // API 요청인 경우 토큰의 'read' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('read')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        // API 요청인 경우 토큰의 'create' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('create')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, $model): bool
    {
        // 사용자는 자신의 프로필을 수정할 수 있음
        if ($this->isSameAccount($user, $model)) {
            return true;
        }

        // API 요청인 경우 토큰의 'update' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('update')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, $model): bool
    {
        // 사용자는 자신을 삭제할 수 없음
        if ($this->isSameAccount($user, $model)) {
            return false;
        }

        // API 요청인 경우 토큰의 'delete' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('delete')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, $model): bool
    {
        // API 요청인 경우 토큰의 'update' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('update')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, $model): bool
    {
        // 사용자는 자신을 영구 삭제할 수 없음
        if ($this->isSameAccount($user, $model)) {
            return false;
        }

        // API 요청인 경우 토큰의 'delete' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('delete')) {
                return false;
            }
        }

        return $user->hasPermissionTo('user-delete');
    }

    /**
     * Determine whether the user can assign roles to the model.
     */
    public function assignRoles($user, $model): bool
    {
        // 사용자는 자신에게 역할을 할당할 수 없음
        if ($this->isSameAccount($user, $model)) {
            return false;
        }

        return $user->hasPermissionTo('user-assign-roles');
    }

    public function assignRolesView($user, $model): bool
    {
        return $this->view($user, $model);
    }

    public function assignRolesUpdate($user, $model): bool
    {
        return $this->assignRoles($user, $model);
    }

    /**
     * Determine whether the user can assign permissions to the model.
     */
    public function assignPermissions($user, $model): bool
    {
        // 사용자는 자신에게 권한을 할당할 수 없음
        if ($this->isSameAccount($user, $model)) {
            return false;
        }

        return $user->hasPermissionTo('user-assign-permissions');
    }

    /**
     * Determine whether the user can view user authentication list.
     */
    public function viewAuthList($user): bool
    {
        return $user->hasPermissionTo('user-view-auth-list');
    }

    private function isSameAccount($user, $model): bool
    {
        if (! method_exists($user, 'getTable') || ! method_exists($model, 'getTable')) {
            return $user->id === $model->id;
        }

        return $user->getTable() === $model->getTable()
            && $user->getKey() === $model->getKey();
    }
}
