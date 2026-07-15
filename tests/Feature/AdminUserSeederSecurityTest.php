<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Ssh521\LaravelAdmin\Database\Seeders\AdminUserSeeder;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminUserSeederSecurityTest extends TestCase
{
    public function test_admin_user_seeding_is_disabled_by_default(): void
    {
        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseCount('admin_users', 0);
    }

    public function test_new_admin_requires_an_explicit_long_password(): void
    {
        config()->set('laravel-admin.seed_admin_user.enabled', true);
        config()->set('laravel-admin.seed_admin_user.email', 'owner@example.com');
        config()->set('laravel-admin.seed_admin_user.password', 'short');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('at least 12 characters');

        $this->seed(AdminUserSeeder::class);
    }

    public function test_explicit_secure_credentials_create_the_admin_user(): void
    {
        config()->set('laravel-admin.seed_admin_user.enabled', true);
        config()->set('laravel-admin.seed_admin_user.email', 'owner@example.com');
        config()->set('laravel-admin.seed_admin_user.password', 'long-random-password');

        $this->seed(AdminUserSeeder::class);

        $admin = AdminUser::query()->where('email', 'owner@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('long-random-password', $admin->password));
        $this->assertTrue($admin->hasRole('Super Admin'));
    }
}
