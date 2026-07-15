<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Services\AdminUserService;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('laravel-admin.seed_admin_user.enabled', false)) {
            $this->command?->info('Admin user seeding is disabled.');

            return;
        }

        $email = config('laravel-admin.seed_admin_user.email');

        if (! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Set a valid LARAVEL_ADMIN_SEED_ADMIN_EMAIL before enabling admin user seeding.');
        }

        $adminUser = $this->findAdminUser($email);
        $adminUserService = app(AdminUserService::class);

        if ($adminUser) {
            $adminUser = $adminUserService->updateAdminUser([
                'adminUserId' => $adminUser->getKey(),
                'name' => config('laravel-admin.seed_admin_user.name', 'Super Admin'),
                'email' => $email,
            ]);
        } else {
            $password = config('laravel-admin.seed_admin_user.password');

            if (! is_string($password) || mb_strlen($password) < 12) {
                throw new InvalidArgumentException('Set LARAVEL_ADMIN_SEED_ADMIN_PASSWORD to at least 12 characters before enabling admin user seeding.');
            }

            $adminUser = $adminUserService->createAdminUser([
                'name' => config('laravel-admin.seed_admin_user.name', 'Super Admin'),
                'email' => $email,
                'password' => $password,
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
