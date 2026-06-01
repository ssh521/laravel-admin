<?php

namespace Ssh521\LaravelAdmin\Validators\AdminUser\Concerns;

trait ResolvesAdminUserTable
{
    private static function adminUsersTable(): string
    {
        return config('laravel-admin.admin_users_table', 'admin_users');
    }
}
