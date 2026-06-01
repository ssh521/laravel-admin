<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser\Concerns;

use Ssh521\LaravelAdmin\Models\AdminUser;

trait ResolvesAdminUserModel
{
    private function adminUserModel(): string
    {
        return config('laravel-admin.admin_user_model', AdminUser::class);
    }
}
