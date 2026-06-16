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

    public function test_token_authenticated_permission_actor_can_view_users(): void
    {
        $user = new UserPolicyTestActor(1, ['read'], ['user-list']);

        $this->assertTrue((new UserPolicy)->viewAny($user));
    }

    public function test_token_authenticated_actor_needs_matching_token_ability_for_write_actions(): void
    {
        $user = new UserPolicyTestActor(1, ['read'], ['user-delete']);
        $model = new UserPolicyTestActor(2);

        $this->assertFalse((new UserPolicy)->delete($user, $model));
    }

    public function test_admin_actor_cannot_delete_their_own_account(): void
    {
        $admin = AdminUser::query()->create([
            'name' => 'Delete Admin',
            'email' => 'delete-admin@example.com',
            'password' => 'password',
        ]);
        $admin->givePermissionTo('user-delete');

        $this->assertFalse((new UserPolicy)->delete($admin, $admin));
        $this->assertFalse((new UserPolicy)->forceDelete($admin, $admin));
    }

    public function test_same_account_can_view_and_update_self_with_matching_token_ability(): void
    {
        $user = new UserPolicyTestActor(1, ['read', 'update']);

        $this->assertTrue((new UserPolicy)->view($user, $user));
        $this->assertTrue((new UserPolicy)->update($user, $user));
    }
}

class UserPolicyTestActor
{
    /**
     * @param  array<int, string>  $tokenAbilities
     * @param  array<int, string>  $permissions
     */
    public function __construct(
        public int $id,
        private readonly array $tokenAbilities = [],
        private readonly array $permissions = [],
    ) {}

    public function hasPermissionTo(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function currentAccessToken(): object
    {
        return (object) ['id' => 1];
    }

    public function tokenCan(string $ability): bool
    {
        return in_array($ability, $this->tokenAbilities, true);
    }

    public function getTable(): string
    {
        return 'users';
    }

    public function getKey(): int
    {
        return $this->id;
    }
}
