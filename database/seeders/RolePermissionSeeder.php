<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    // 초기 퍼미션 세팅
    private $permissions = [

        // Super Admin 권한
        'super-admin-manage', // 관리자 관리
        'super-admin-settings', // 설정 관리

        // Admin Dashboard 권한
        'admin-view-logs', // 로그 관리
        'admin-manage-menus', // 메뉴 관리
        'admin-manage-permissions', // 권한 관리
        'admin-manage-roles', // 역할 관리
        'admin-view-statistics', // 통계 관리
        'admin-dashboard-access',

        // Role 관련 권한
        'role-list',
        'role-view',
        'role-create',
        'role-edit',
        'role-delete',
        'role-assign-permissions',
        'role-view-permissions',

        // Permission 관련 권한
        'permission-list',
        'permission-view',
        'permission-create',
        'permission-edit',
        'permission-delete',
        'permission-assign-to-roles',

        // Menu 관련 권한
        'menu-list',
        'menu-view',
        'menu-create',
        'menu-edit',
        'menu-delete',

        // Menu Category 관련 권한
        'menu-category-list',
        'menu-category-view',
        'menu-category-create',
        'menu-category-edit',
        'menu-category-delete',
    ];

    private $obsoletePermissions = [
        'permission-assign-to-users',
        'permission-view-assignments',
        'super-admin-users',
        'admin-manage-users',
        'user-list',
        'user-view',
        'user-create',
        'user-edit',
        'user-delete',
        'user-view-auth-list',
        'user-assign-roles',
        'user-assign-permissions',
    ];

    public function run(): void
    {
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');

        Permission::query()
            ->where('guard_name', $guardName)
            ->whereIn('name', $this->obsoletePermissions)
            ->delete();

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        $permissions = Permission::where('guard_name', $guardName)->pluck('id', 'id')->all();

        // 역할들을 만든다.
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => $guardName,
        ]);

        // Super Admin 역할에 모든 권한 부여
        $superAdminRole->givePermissionTo($permissions);

        // 관리자 역할
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => $guardName,
        ]);
        // Admin 역할도 기본 설치 시 모든 권한을 가진다.
        $adminRole->givePermissionTo($permissions);

        // 실행시키기
        // php artisan db:seed RolePermissionSeeder

        $this->command?->info('Super Admin and Admin roles are ready. Seed an admin user with: php artisan db:seed --class="Ssh521\\\\LaravelAdmin\\\\Database\\\\Seeders\\\\AdminUserSeeder"');
    }
}
