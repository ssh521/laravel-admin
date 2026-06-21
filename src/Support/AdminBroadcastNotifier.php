<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Throwable;

class AdminBroadcastNotifier
{
    /**
     * @var array<string, string>
     */
    private const RESOURCE_ROUTE_NAMES = [
        'role' => 'roles.show',
        'permission' => 'permissions.show',
        'menu' => 'menus.show',
        'menu_category' => 'menu-categories.show',
    ];

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
            includeUrl: false,
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

    /**
     * @param  array<string, mixed>  $payload
     */
    public function resourceChanged(
        Model $resource,
        string $resourceType,
        string $event,
        string $title,
        string $type = 'info',
        ?string $body = null,
        ?string $url = null,
        bool $includeUrl = true,
        array $payload = [],
    ): void {
        if (! $this->isEnabled()) {
            return;
        }

        $facade = $this->facade();

        if (! class_exists($facade) || ! is_callable([$facade, 'to'])) {
            return;
        }

        try {
            $routeName = $this->resourceRouteName($resourceType);

            $facade::to($this->adminRecipients())
                ->type($type)
                ->title($title)
                ->body($body ?? $this->resourceLabel($resource))
                ->url($includeUrl ? ($url ?? $this->resourceUrl($resource, $routeName)) : null)
                ->payload($payload + [
                    'resource' => $resourceType,
                    'resource_id' => $resource->getKey(),
                    'event' => $event,
                    'route_name' => $routeName,
                ])
                ->source('laravel-admin')
                ->related($resource)
                ->actor($this->actor())
                ->send();
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function sendAdminUserNotification(
        Model $adminUser,
        string $type,
        string $title,
        string $event,
        ?string $url = null,
        bool $includeUrl = true,
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
                ->url($includeUrl ? ($url ?? $this->adminUserUrl($adminUser)) : null)
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

    private function resourceRouteName(string $resourceType): ?string
    {
        $routeSuffix = self::RESOURCE_ROUTE_NAMES[$resourceType] ?? null;

        if ($routeSuffix === null) {
            return null;
        }

        $routeName = config('laravel-admin.route_name_prefix', 'admin.').$routeSuffix;

        return Route::has($routeName) ? $routeName : null;
    }

    private function resourceUrl(Model $resource, ?string $routeName): ?string
    {
        if ($routeName === null || ! $resource->exists) {
            return null;
        }

        return route($routeName, $resource->getKey());
    }

    private function resourceLabel(Model $resource): ?string
    {
        $label = collect(['name', 'title', 'email', 'description'])
            ->map(fn (string $attribute): string => (string) ($resource->getAttribute($attribute) ?? ''))
            ->filter()
            ->implode(' ');

        return trim($label) ?: null;
    }
}
