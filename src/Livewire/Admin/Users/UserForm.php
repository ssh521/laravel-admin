<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Services\UserService;

class UserForm extends Component
{
    use AuthorizesRequests;

    public $showModal = false;

    public $mode = 'create'; // create | view | edit

    public $parentModalId = null; // 부모 draggable-modal의 ID

    public $user;

    public $userId;

    public $name = '';

    public $email = '';

    public $password = '';

    public function __construct()
    {
        $this->userService = app(UserService::class);
        $this->useAdminGuard();
    }

    #[On('admin-users:user-form:open')]
    public function open(array $payload = []): void
    {
        $this->useAdminGuard();
        $this->resetValidation();
        $this->resetForm();

        $mode = $payload['mode'] ?? 'create';
        $this->mode = in_array($mode, ['create', 'view', 'edit'], true) ? $mode : 'create';

        $userId = $payload['userId'] ?? $payload['id'] ?? null;
        // 이벤트에서 전달된 parentModalId가 있으면 사용, 없으면 초기 속성 유지
        $this->parentModalId = $payload['parentModalId'] ?? $this->parentModalId;

        if ($this->mode === 'create') {
            $this->authorize('create', User::class);
        }

        if (in_array($this->mode, ['view', 'edit'], true) && $userId) {
            $this->user = $this->userService->getUserDetail(['userId' => $userId]);
            $this->authorize($this->mode === 'view' ? 'view' : 'update', $this->user);

            $this->userId = $this->user->id;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
        }

        $this->showModal = true;
    }

    #[On('close-modal')]
    public function handleCloseModal(array $payload = []): void
    {
        // draggable-modal이 닫힐 때도 UserForm 닫기
        $this->close();
    }

    public function close(): void
    {
        // 이미 닫혀있으면 중복 호출 방지
        if (! $this->showModal) {
            return;
        }

        $this->showModal = false;

        // 독립적으로 사용될 때만 자체 모달 UI를 사용하므로 여기서는 모달 닫기 이벤트를 디스패치하지 않음
        // draggable-modal 안에 있을 때는 부모 모달이 자식 모달을 닫는 것을 처리함
    }

    public function save(): void
    {
        $this->useAdminGuard();

        if ($this->mode === 'view') {
            return;
        }

        try {
            if ($this->mode === 'create') {
                $this->authorize('create', User::class);
                $user = $this->userService->createUser([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password ?: 'changeme1234!',
                ]);
            } else {
                $user = User::findOrFail($this->userId);
                $this->authorize('update', $user);
                $this->userService->updateUser([
                    'userId' => $this->userId,
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password ?: null,
                ]);
            }

            $this->dispatch('admin-users:user:saved');

            // 자식 모달인 경우 모달 닫기
            if ($this->parentModalId) {
                $this->js("
                    window.dispatchEvent(new CustomEvent('close-modal', { 
                        detail: { modalId: '{$this->parentModalId}' } 
                    }));
                ");
            }

            $this->showModal = false;
        } catch (ValidationException $e) {
            $this->resetValidation();
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        }
    }

    protected function resetForm(): void
    {
        $this->user = null;
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
    }

    public function render()
    {
        $this->useAdminGuard();

        return view('livewire.admin.users.user-form');
    }

    private UserService $userService;

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }
}
