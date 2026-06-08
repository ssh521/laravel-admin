<?php

use App\Models\User;
use Ssh521\LaravelAdmin\Http\Middleware\AuthenticateAdmin;
use Ssh521\LaravelAdmin\Models\AdminUser;

return [
    'route_prefix' => 'admin',
    'route_name_prefix' => 'admin.',
    'middleware' => ['web', AuthenticateAdmin::class, 'verified'],
    'auth' => [
        'guard' => 'laravel_admin',
        'provider' => 'laravel_admin_users',
        'admin_login' => [
            'enabled' => true,
            'middleware' => ['web'],
            'redirect_to' => '/admin',
        ],
    ],
    'api_prefix' => 'api/admin',
    'api_middleware' => ['api'],
    'admin_user_model' => AdminUser::class,
    'admin_users_table' => 'admin_users',
    'seed_admin_user' => [
        'enabled' => env('LARAVEL_ADMIN_SEED_ADMIN_ENABLED', true),
        'name' => env('LARAVEL_ADMIN_SEED_ADMIN_NAME', 'Super Admin'),
        'email' => env('LARAVEL_ADMIN_SEED_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('LARAVEL_ADMIN_SEED_ADMIN_PASSWORD', 'change-this-password'),
        'verified' => env('LARAVEL_ADMIN_SEED_ADMIN_VERIFIED', true),
        'role' => env('LARAVEL_ADMIN_SEED_ADMIN_ROLE', 'Super Admin'),
    ],
    'user_model' => User::class,
    'super_admin_role' => 'Super Admin',
    'vite' => [
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ],
];
