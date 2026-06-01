<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\UserService;

class UserDetailModal extends Component
{
    use AuthorizesRequests;

    public $user = null;

    private UserService $userService;

    public function boot(UserService $userService): void
    {
        $this->userService = $userService;
    }

    // 사용자 상세 모달 열기
    #[On('admin-users:user-detail-modal:open')]
    public function open(array $payload = []): void
    {
        $userId = $payload['userId'] ?? null;

        if (! $userId) {
            return;
        }

        $this->user = $this->userService->getUserDetail(['userId' => $userId]);

        if (! $this->user) {
            return;
        }

        $this->authorize('view', $this->user);

        // window.dispatchEvent를 사용하여 모달 열기로 해야함. 이벤트.window 에 적용될려면 js 로 해야함.
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'user-detail-modal' } 
            }));
        ");
    }

    // 사용자 수정 (Blade 페이지로 이동)
    public function editUser(): void
    {
        if (! $this->user) {
            return;
        }

        $this->authorize('update', $this->user);

        $this->redirect(route('admin.users.edit', $this->user->id));
    }

    // 사용자 상세 모달 열기 (자식 모달로 열기)
    public function openUserDetailModal(): void
    {
        if (! $this->user) {
            return;
        }

        // UserForm을 자식 모달로 열기
        $this->dispatch('admin-users:user-form:open', [
            'mode' => 'view',
            'userId' => $this->user->id,
            'parentModalId' => 'user-edit-modal',
        ]);

        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'user-edit-modal' } 
            }));
        ");
    }

    // 사용자 수정 (자식 모달로 열기)
    public function openUserEditModal(): void
    {
        if (! $this->user) {
            return;
        }

        // UserForm을 자식 모달로 열기
        $this->dispatch('admin-users:user-form:open', [
            'mode' => 'edit',
            'userId' => $this->user->id,
            'parentModalId' => 'user-edit-modal',
        ]);

        // draggable-modal 열기
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'user-edit-modal' } 
            }));
        ");
    }

    public function openRoleViewModal(): void
    {
        if (! $this->user) {
            return;
        }

        // UserRoleForm을 자식 모달로 열기
        $this->dispatch('admin-users:user-role-form:open', [
            'mode' => 'view',
            'id' => $this->user->id,
            'parentModalId' => 'user-role-edit-modal',
        ]);

        // draggable-modal 열기
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'user-role-edit-modal' } 
            }));
        ");
    }

    public function openRoleEditModal(): void
    {
        if (! $this->user) {
            return;
        }

        $this->authorize('assignRoles', $this->user);

        // UserRoleForm을 자식 모달로 열기
        $this->dispatch('admin-users:user-role-form:open', [
            'mode' => 'edit',
            'id' => $this->user->id,
            'parentModalId' => 'user-role-edit-modal',
        ]);

        // draggable-modal 열기
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'user-role-edit-modal' } 
            }));
        ");
    }

    public function setEmailVerified(int $userId, bool|int|string $isVerified): void
    {
        $user = $this->userService->getUserDetail(['userId' => $userId]);
        $this->authorize('update', $user);

        $updatedUser = $this->userService->setUserEmailVerification([
            'userId' => $userId,
            'isVerified' => $isVerified,
        ]);

        $this->user = $this->userService->getUserDetail(['userId' => $userId]);

        $this->dispatch('admin-users:user:saved');

        $message = $updatedUser->email_verified_at ? '이메일을 인증 처리했습니다.' : '이메일 인증을 해제했습니다.';

        $this->dispatch('notification:show', message: $message, type: 'success');
    }

    #[On('admin-users:user:saved')]
    public function handleUserSaved(): void
    {
        // 사용자가 저장된 후 사용자 정보 다시 로드
        if ($this->user) {
            $this->user = $this->userService->getUserDetail(['userId' => $this->user->id]);
        }
    }

    #[On('admin-users:user-roles:updated')]
    public function handleUserRolesUpdated(): void
    {
        // 역할이 업데이트된 후 사용자 정보 다시 로드
        if ($this->user) {
            $this->user = $this->userService->getUserDetail(['userId' => $this->user->id]);
        }
    }

    public function resetUser(): void
    {
        $this->user = null;
    }

    public function render()
    {
        return view('livewire.admin.users.user-detail-modal');
    }
}
