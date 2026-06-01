<?php

namespace Ssh521\LaravelAdmin\Models;

use App\Models\User as BaseUser;

class User extends BaseUser
{
    public function guardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }
}
