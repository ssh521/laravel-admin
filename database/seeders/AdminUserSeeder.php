<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Services\AdminUserService;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('laravel-admin.seed_admin_user.enabled', true)) {
            $this->command?->info('Admin user seeding is disabled.');

            return;
        }

        $email = config('laravel-admin.seed_admin_user.email', 'admin@example.com');
        $adminUser = $this->findAdminUser($email);
        $adminUserService = app(AdminUserService::class);

        if ($adminUser) {
            $adminUser = $adminUserService->updateAdminUser([
                'adminUserId' => $adminUser->getKey(),
                'name' => config('laravel-admin.seed_admin_user.name', 'Super Admin'),
                'email' => $email,
            ]);
        } else {
            $adminUser = $adminUserService->createAdminUser([
                'name' => config('laravel-admin.seed_admin_user.name', 'Super Admin'),
                'email' => $email,
                'password' => config('laravel-admin.seed_admin_user.password', 'change-this-password'),
                'isVerified' => config('laravel-admin.seed_admin_user.verified', true),
            ]);
        }

        $adminUserService->setAdminUserEmailVerification([
            'adminUserId' => $adminUser->getKey(),
            'isVerified' => config('laravel-admin.seed_admin_user.verified', true),
        ]);

        $role = $this->ensureAdminRole();

        $adminUserService->assignRolesToAdminUser([
            'adminUserId' => $adminUser->getKey(),
            'roleIds' => [$role->id],
        ]);

        $this->command?->info("Admin user [{$email}] is ready.");
    }

    private function findAdminUser(string $email): ?Model
    {
        $adminUserModel = config('laravel-admin.admin_user_model', AdminUser::class);

        return $adminUserModel::query()
            ->where('email', $email)
            ->first();
    }

    private function ensureAdminRole(): Role
    {
        return Role::firstOrCreate([
            'name' => config('laravel-admin.seed_admin_user.role', config('laravel-admin.super_admin_role', 'Super Admin')),
            'guard_name' => config('laravel-admin.auth.guard', 'laravel_admin'),
        ]);
    }
}
