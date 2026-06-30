<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\UserService;

class UserEditModal extends Component
{
    use AuthorizesRequests;

    public int|string $userId;

    public ?string $modalStackId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

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

    public function save(): void
    {
        $this->useAdminGuard();

        try {
            $user = $this->userService->getUserDetail(['userId' => $this->userId]);
            $this->authorize('update', $user);

            $this->userService->updateUser([
                'userId' => $this->userId,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password !== '' ? $this->password : null,
            ]);

            $this->dispatch('admin-user-management:user:saved');
            $this->dispatch('user-saved', userId: $this->userId);
            $this->dispatch('notification:show', message: '사용자 정보가 저장되었습니다.', type: 'success');

            if ($this->modalStackId) {
                $this->dispatch('admin:modal-stack:close', id: $this->modalStackId);
            }
        } catch (ValidationException $e) {
            $this->resetValidation();

            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        }
    }

    private function loadUser(): void
    {
        $this->user = $this->userService->getUserDetail(['userId' => $this->userId]);
        $this->authorize('update', $this->user);

        $this->name = (string) $this->user->name;
        $this->email = (string) $this->user->email;
        $this->password = '';
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.users.user-edit-modal');
    }
}
