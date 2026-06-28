<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Ssh521\LaravelAdmin\Support\AdminPackageRegistrar;

class UserManagementSeeder extends Seeder
{
    public function run(): void
    {
        app(AdminPackageRegistrar::class)->register('laravel-admin-user-management', [
            'category' => [
                'name' => '회원 관리',
                'sort_order' => 200,
                'is_active' => true,
            ],
            'menus' => [
                [
                    'name' => '회원',
                    'route_name' => 'admin.users.index',
                    'url' => '/admin/users',
                    'target' => '_self',
                    'sort_order' => 0,
                    'is_active' => true,
                    'icon' => 'fas fa-user',
                    'description' => '사용자 목록',
                ],
            ],
            'permissions' => [
                ['name' => 'user-list'],
                ['name' => 'user-view'],
                ['name' => 'user-create'],
                ['name' => 'user-edit'],
                ['name' => 'user-delete'],
                ['name' => 'user-view-auth-list'],
            ],
        ]);
    }
}
