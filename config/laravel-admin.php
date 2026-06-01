<?php

use App\Models\User;

return [
    'route_prefix' => 'admin',
    'route_name_prefix' => 'admin.',
    'middleware' => ['web', 'auth', 'verified'],
    'api_prefix' => 'api/admin',
    'api_middleware' => ['api'],
    'user_model' => User::class,
    'super_admin_role' => 'Super Admin',
    'vite' => [
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ],
];
