<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Throwable;

class AdminBroadcastNotifier
{
    public function adminUserCreated(Model $adminUser): void
    {
        $this->sendAdminUserNotification(
            adminUser: $adminUser,
            type: 'info',
            title: '관리자 계정이 생성되었습니다',
            event: 'created',
        );
    }

    public function adminUserUpdated(Model $adminUser): void
    {
        $this->sendAdminUserNotification(
            adminUser: $adminUser,
            type: 'info',
            title: '관리자 계정이 수정되었습니다',
            event: 'updated',
        );
    }

    public function adminUserDeleted(Model $adminUser): void
    {
        $this->sendAdminUserNotification(
            adminUser: $adminUser,
            type: 'warning',
            title: '관리자 계정이 삭제되었습니다',
            event: 'deleted',
            url: null,
        );
    }

    public function adminUserRolesUpdated(Model $adminUser): void
    {
        $this->sendAdminUserNotification(
            adminUser: $adminUser,
            type: 'warning',
            title: '관리자 권한이 변경되었습니다',
            event: 'roles_updated',
        );
    }

    public function adminUserEmailVerificationUpdated(Model $adminUser, bool $isVerified): void
    {
        $this->sendAdminUserNotification(
            adminUser: $adminUser,
            type: $isVerified ? 'success' : 'warning',
            title: $isVerified ? '관리자 이메일 인증이 완료되었습니다' : '관리자 이메일 인증이 해제되었습니다',
            event: $isVerified ? 'email_verified' : 'email_unverified',
        );
    }

    private function sendAdminUserNotification(
        Model $adminUser,
        string $type,
        string $title,
        string $event,
        ?string $url = null,
    ): void {
        if (! $this->isEnabled()) {
            return;
        }

        $facade = $this->facade();

        if (! class_exists($facade) || ! is_callable([$facade, 'to'])) {
            return;
        }

        try {
            $facade::to($this->adminRecipients())
                ->type($type)
                ->title($title)
                ->body($this->adminUserLabel($adminUser))
                ->url($url ?? $this->adminUserUrl($adminUser))
                ->payload([
                    'resource' => 'admin_user',
                    'resource_id' => $adminUser->getKey(),
                    'event' => $event,
                    'route_name' => $this->adminUserRouteName(),
                ])
                ->source('laravel-admin')
                ->related($adminUser)
                ->actor($this->actor())
                ->send();
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function isEnabled(): bool
    {
        return config('laravel-admin.broadcast_notifications.enabled', true) === true;
    }

    private function facade(): string
    {
        return (string) config(
            'laravel-admin.broadcast_notifications.facade',
            'Ssh521\\LaravelBroadcastNotification\\Facades\\BroadcastNotification'
        );
    }

    /**
     * @return Collection<int, Model>
     */
    private function adminRecipients(): Collection
    {
        $adminUserModel = config('laravel-admin.admin_user_model');

        if (! is_string($adminUserModel) || ! method_exists($adminUserModel, 'query')) {
            return collect();
        }

        return $adminUserModel::query()->get();
    }

    private function actor(): ?Model
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $user = Auth::guard($guard)->user();

        return $user instanceof Model ? $user : null;
    }

    private function adminUserUrl(Model $adminUser): ?string
    {
        $routeName = $this->adminUserRouteName();

        if ($routeName === null) {
            return null;
        }

        return route($routeName, $adminUser->getKey());
    }

    private function adminUserRouteName(): ?string
    {
        $routeName = config('laravel-admin.route_name_prefix', 'admin.').'admin-users.show';

        return Route::has($routeName) ? $routeName : null;
    }

    private function adminUserLabel(Model $adminUser): ?string
    {
        $name = (string) ($adminUser->getAttribute('name') ?? '');
        $email = (string) ($adminUser->getAttribute('email') ?? '');

        return trim($name.' '.$email) ?: null;
    }
}
