<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Services\AdminUserService;
use Ssh521\LaravelAdmin\Support\AdminBroadcastNotifier;
use Ssh521\LaravelAdmin\Tests\Fixtures\FakeBroadcastNotification;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminBroadcastNotifierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        FakeBroadcastNotification::reset();
        config()->set('laravel-admin.broadcast_notifications.facade', FakeBroadcastNotification::class);
    }

    public function test_it_skips_notifications_when_optional_package_is_not_installed(): void
    {
        config()->set('laravel-admin.broadcast_notifications.facade', 'Missing\\BroadcastNotification\\Facade');

        $adminUser = $this->adminUserService()->createAdminUser([
            'name' => 'No Package Admin',
            'email' => 'no-package@example.com',
            'password' => 'password123',
            'isVerified' => true,
        ]);

        $this->assertInstanceOf(AdminUser::class, $adminUser);
        $this->assertSame([], FakeBroadcastNotification::$sent);
    }

    public function test_it_can_disable_broadcast_notifications_by_config(): void
    {
        config()->set('laravel-admin.broadcast_notifications.enabled', false);

        $this->adminUserService()->createAdminUser([
            'name' => 'Disabled Admin',
            'email' => 'disabled@example.com',
            'password' => 'password123',
        ]);

        $this->assertSame([], FakeBroadcastNotification::$sent);
    }

    public function test_it_sends_admin_user_created_notification_when_integration_is_available(): void
    {
        AdminUser::query()->create([
            'name' => 'Existing Admin',
            'email' => 'existing@example.com',
            'password' => 'password',
        ]);

        $adminUser = $this->adminUserService()->createAdminUser([
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'password123',
            'isVerified' => true,
        ]);

        $this->assertCount(1, FakeBroadcastNotification::$sent);

        $notification = FakeBroadcastNotification::$sent[0];

        $this->assertSame('info', $notification['type']);
        $this->assertSame('관리자 계정이 생성되었습니다', $notification['title']);
        $this->assertSame('laravel-admin', $notification['source']);
        $this->assertSame($adminUser->getKey(), $notification['payload']['resource_id']);
        $this->assertSame('created', $notification['payload']['event']);
        $this->assertSame('admin_user', $notification['payload']['resource']);
        $this->assertSame($adminUser->getKey(), $notification['related']->getKey());
        $this->assertCount(2, $notification['notifiables']);
    }

    public function test_it_sends_admin_resource_changed_notification_when_integration_is_available(): void
    {
        AdminUser::query()->create([
            'name' => 'Existing Admin',
            'email' => 'existing@example.com',
            'password' => 'password',
        ]);

        $role = Role::query()->create([
            'name' => 'Editor',
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        app(AdminBroadcastNotifier::class)->resourceChanged(
            resource: $role,
            resourceType: 'role',
            event: 'updated',
            title: '관리자 역할이 수정되었습니다',
            type: 'warning',
        );

        $this->assertCount(1, FakeBroadcastNotification::$sent);

        $notification = FakeBroadcastNotification::$sent[0];

        $this->assertSame('warning', $notification['type']);
        $this->assertSame('관리자 역할이 수정되었습니다', $notification['title']);
        $this->assertSame('laravel-admin', $notification['source']);
        $this->assertSame('role', $notification['payload']['resource']);
        $this->assertSame($role->getKey(), $notification['payload']['resource_id']);
        $this->assertSame('updated', $notification['payload']['event']);
        $this->assertSame('admin.roles.show', $notification['payload']['route_name']);
        $this->assertSame($role->getKey(), $notification['related']->getKey());
        $this->assertCount(1, $notification['notifiables']);
    }

    private function adminUserService(): AdminUserService
    {
        return app(AdminUserService::class);
    }
}
