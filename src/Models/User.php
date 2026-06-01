<?php

namespace Ssh521\LaravelAdmin\Models;

use App\Models\User as BaseUser;

class User extends BaseUser
{
    public function guardName(): string
    {
        return config('laravel-admin.auth.guard', 'laravel_admin');
    }
}
