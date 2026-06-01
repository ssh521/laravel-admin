<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Ssh521\LaravelAdmin\Models\AdminNotice;

class AdminNoticePolicy
{
    use HandlesAuthorization;

    public function uploadImage($user): bool
    {
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            return $request->user()->tokenCan('create') || $request->user()->tokenCan('update');
        }

        return $user->hasPermissionTo('notice-create') || $user->hasPermissionTo('notice-edit');
    }

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

        return $user->hasPermissionTo('notice-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, AdminNotice $adminNotice): bool
    {
        // API 요청인 경우 토큰의 'read' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('read')) {
                return false;
            }
        }

        return $user->hasPermissionTo('notice-view');
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

        return $user->hasPermissionTo('notice-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, AdminNotice $adminNotice): bool
    {
        // API 요청인 경우 토큰의 'update' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('update')) {
                return false;
            }
        }

        return $user->hasPermissionTo('notice-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, AdminNotice $adminNotice): bool
    {
        // API 요청인 경우 토큰의 'delete' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('delete')) {
                return false;
            }
        }

        return $user->hasPermissionTo('notice-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, AdminNotice $adminNotice): bool
    {
        // API 요청인 경우 토큰의 'update' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('update')) {
                return false;
            }
        }

        return $user->hasPermissionTo('notice-edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, AdminNotice $adminNotice): bool
    {
        // API 요청인 경우 토큰의 'delete' ability 확인
        $request = request();
        if ($request->user()?->currentAccessToken()) {
            if (! $request->user()->tokenCan('delete')) {
                return false;
            }
        }

        return $user->hasPermissionTo('notice-delete');
    }
}
