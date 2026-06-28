<?php

use Ssh521\LaravelAdmin\Http\Middleware\AuthenticateAdmin;
use Ssh521\LaravelAdmin\Models\AdminUser;

return [
    'route_prefix' => 'admin',
    'route_name_prefix' => 'admin.',
    'middleware' => ['web', AuthenticateAdmin::class, 'verified'],

    /*
    |--------------------------------------------------------------------------
    | 관리자 인증 설정
    |--------------------------------------------------------------------------
    |
    | Laravel Admin은 일반 회원과 관리자 계정을 분리하기 위해 별도의
    | session guard와 user provider를 사용합니다. 서비스 프로바이더가
    | 실행 시점에 auth.guards.{guard}, auth.providers.{provider} 설정을
    | 동적으로 등록합니다.
    |
    | provider에서 사용할 모델은 아래 admin_user_model 설정으로 결정됩니다.
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | 관리자 계정 모델
    |--------------------------------------------------------------------------
    |
    | 이 모델은 위 laravel_admin_users provider에서 사용됩니다.
    | 관리자와 일반 회원의 로그인 화면, 계정 생명주기, 권한 정책이 다르면
    | 일반 회원 모델과 분리해서 유지하는 것이 좋습니다.
    |
    */
    'admin_user_model' => AdminUser::class,
    'admin_users_table' => 'admin_users',

    /*
    |--------------------------------------------------------------------------
    | 일반 회원 모델
    |--------------------------------------------------------------------------
    |
    | 관리자 화면에서 관리할 사이트 회원 모델입니다. Laravel starter kit의
    | 기본 App\Models\User를 사용하며, 호스트 앱이 다른 회원 모델을 쓰면
    | 이 값만 교체하면 됩니다.
    |
    */
    'user_model' => App\Models\User::class,
    'users' => [
        'admin' => [
            'route_prefix' => 'users',
            'route_name_prefix' => 'users.',
            'middleware' => null,
        ],
        'api' => [
            'enabled' => false,
            'route_prefix' => 'users',
            'middleware' => ['auth:sanctum'],
        ],
    ],

    'seed_admin_user' => [
        'enabled' => env('LARAVEL_ADMIN_SEED_ADMIN_ENABLED', true),
        'name' => env('LARAVEL_ADMIN_SEED_ADMIN_NAME', 'Super Admin'),
        'email' => env('LARAVEL_ADMIN_SEED_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('LARAVEL_ADMIN_SEED_ADMIN_PASSWORD', 'change-this-password'),
        'verified' => env('LARAVEL_ADMIN_SEED_ADMIN_VERIFIED', true),
        'role' => env('LARAVEL_ADMIN_SEED_ADMIN_ROLE', 'Super Admin'),
    ],
    'super_admin_role' => 'Super Admin',
    'broadcast_notifications' => [
        'enabled' => env('LARAVEL_ADMIN_BROADCAST_NOTIFICATIONS_ENABLED', true),
        'facade' => 'Ssh521\\LaravelBroadcastNotification\\Facades\\BroadcastNotification',
    ],
    'vite' => [
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ],
];
