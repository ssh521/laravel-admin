<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Policies\UserPolicy;
use Ssh521\LaravelAdmin\Tests\TestCase;

class UserPolicyTest extends TestCase
{
    public function test_admin_user_can_view_users_with_permission(): void
    {
        $admin = AdminUser::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $admin->assignRole('Admin');

        $this->assertTrue((new UserPolicy)->viewAny($admin));
    }

    public function test_non_admin_actor_cannot_view_users_even_with_matching_methods(): void
    {
        $user = new class
        {
            public function hasPermissionTo(string $permission): bool
            {
                return true;
            }
        };

        $this->assertFalse((new UserPolicy)->viewAny($user));
    }
}
