<?php

namespace Ssh521\LaravelAdmin\Livewire\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

trait AuthorizesMenuEditing
{
    private function authorizeMenuEdit(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $user = Auth::guard($guard)->user();
        $superAdminRole = config('laravel-admin.super_admin_role', 'Super Admin');

        if ($user && ($user->hasRole($superAdminRole) || $user->hasPermissionTo('menu-edit'))) {
            return;
        }

        throw new AuthorizationException;
    }
}
