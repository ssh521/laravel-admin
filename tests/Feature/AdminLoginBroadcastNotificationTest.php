<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Tests\Fixtures\FakeBroadcastNotification;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminLoginBroadcastNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        FakeBroadcastNotification::reset();
        config()->set('laravel-admin.broadcast_notifications.facade', FakeBroadcastNotification::class);
    }

    public function test_it_sends_admin_login_notification_after_successful_login(): void
    {
        $adminUser = AdminUser::query()->create([
            'name' => 'Login Controller Admin',
            'email' => 'login-controller@example.com',
            'password' => 'password123',
        ]);

        $this->post('/admin/login', [
            'email' => 'login-controller@example.com',
            'password' => 'password123',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($adminUser, config('laravel-admin.auth.guard', 'laravel_admin'));
        $this->assertCount(1, FakeBroadcastNotification::$sent);

        $notification = FakeBroadcastNotification::$sent[0];

        $this->assertSame('system', $notification['type']);
        $this->assertSame('관리자가 로그인했습니다', $notification['title']);
        $this->assertSame('Login Controller Admin login-controller@example.com', $notification['body']);
        $this->assertSame('logged_in', $notification['payload']['event']);
        $this->assertSame('admin_user', $notification['payload']['resource']);
        $this->assertSame($adminUser->getKey(), $notification['payload']['resource_id']);
    }

    public function test_it_does_not_send_admin_login_notification_after_failed_login(): void
    {
        AdminUser::query()->create([
            'name' => 'Failed Login Admin',
            'email' => 'failed-login@example.com',
            'password' => 'password123',
        ]);

        $this->from('/admin/login')->post('/admin/login', [
            'email' => 'failed-login@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertSame([], FakeBroadcastNotification::$sent);
    }
}
