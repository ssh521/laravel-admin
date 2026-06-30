<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\UserService;

class UserShowModal extends Component
{
    use AuthorizesRequests;

    public int|string $userId;

    public ?string $modalStackId = null;

    public object $user;

    private UserService $userService;

    public function boot(UserService $userService): void
    {
        $this->userService = $userService;
        $this->useAdminGuard();
    }

    public function mount(int|string $userId, ?string $modalStackId = null): void
    {
        $this->userId = $userId;
        $this->modalStackId = $modalStackId;

        $this->loadUser();
    }

    public function editUser(): void
    {
        $this->authorize('update', $this->user);

        $this->redirect(route('admin.users.edit', $this->user->id));
    }

    public function openUserEditModal(): void
    {
        $this->authorize('update', $this->user);

        $this->dispatch('admin:modal-stack:push', [
            'id' => 'user-edit-'.$this->user->id.'-'.uniqid(),
            'component' => 'admin.users.user-edit-modal',
            'params' => ['userId' => $this->user->id],
            'title' => '사용자 수정',
            'size' => 'md',
        ]);
    }

    public function setEmailVerified(int $userId, bool|int|string $isVerified): void
    {
        $user = $this->userService->getUserDetail(['userId' => $userId]);
        $this->authorize('update', $user);

        $updatedUser = $this->userService->setUserEmailVerification([
            'userId' => $userId,
            'isVerified' => $isVerified,
        ]);

        $this->loadUser();

        $this->dispatch('admin-user-management:user:saved');
        $this->dispatch('user-saved', userId: $this->userId);

        $message = $updatedUser->email_verified_at ? '이메일을 인증 처리했습니다.' : '이메일 인증을 해제했습니다.';

        $this->dispatch('notification:show', message: $message, type: 'success');
    }

    #[On('admin-user-management:user:saved')]
    public function handleUserSaved(): void
    {
        $this->loadUser();
    }

    private function loadUser(): void
    {
        $this->user = $this->userService->getUserDetail(['userId' => $this->userId]);
        $this->authorize('view', $this->user);
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.users.user-show-modal');
    }
}
