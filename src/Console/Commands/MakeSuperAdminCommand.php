<?php

namespace Ssh521\LaravelAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;

class MakeSuperAdminCommand extends Command
{
    protected $signature = 'laravel-admin:make-super-admin
        {email : The email address of the admin user to promote}
        {--name= : Name to use when creating a new admin user}
        {--password= : Password to use when creating a new admin user}
        {--verified : Mark the admin user email as verified}';

    protected $description = 'Create or promote an admin user as the Laravel Admin Super Admin.';

    public function handle(): int
    {
        $userModel = config('laravel-admin.admin_user_model', AdminUser::class);
        $roleName = config('laravel-admin.super_admin_role', 'Super Admin');
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');
        $email = (string) $this->argument('email');

        if (! method_exists($userModel, 'query')) {
            $this->error("Configured admin user model [{$userModel}] is invalid.");

            return self::FAILURE;
        }

        $user = $userModel::query()->where('email', $email)->first();

        if (! $user) {
            $password = $this->option('password') ?: $this->secret('Password for the new Super Admin');

            if (! $password) {
                $this->error('A password is required when creating a new Super Admin user.');

                return self::FAILURE;
            }

            $user = $userModel::query()->create([
                'name' => $this->option('name') ?: 'Super Admin',
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            if ($this->option('verified')) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        } elseif ($this->option('verified') && empty($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        if (! method_exists($user, 'assignRole')) {
            $this->error("Admin user model [{$userModel}] must use Spatie\\Permission\\Traits\\HasRoles.");

            return self::FAILURE;
        }

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => $guardName,
        ]);

        $user->assignRole($role);

        $this->info("{$email} is now assigned to [{$roleName}].");

        return self::SUCCESS;
    }
}
