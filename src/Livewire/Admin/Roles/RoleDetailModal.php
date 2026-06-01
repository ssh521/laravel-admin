<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\RoleService;

class RoleDetailModal extends Component
{
    use AuthorizesRequests;

    public $role = null;

    private RoleService $roleService;

    public function boot(RoleService $roleService): void
    {
        $this->roleService = $roleService;
    }

    #[On('admin-roles:role-detail-modal:show')]
    public function showRoleDetail($params): void
    {
        $roleId = is_array($params) ? $params[0] : $params;
        $this->role = $this->roleService->getRoleDetail(['roleId' => $roleId]);

        if (! $this->role) {
            return;
        }

        $this->authorize('view', $this->role);

        // window.dispatchEvent를 사용하여 모달 열기로 해야함. 이벤트.window 에 적용될려면 js 로 해야함.
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'role-detail-modal' } 
            }));
        ");
    }

    public function editRole(): void
    {
        if (! $this->role) {
            return;
        }

        $this->authorize('update', $this->role);

        $this->redirect(route('admin.roles.edit', $this->role->id));
    }

    public function openEditModal(): void
    {
        if (! $this->role) {
            return;
        }

        $this->authorize('update', $this->role);

        // RoleForm을 자식 모달로 열기
        $this->dispatch('admin-roles:role-form:open', [
            'mode' => 'edit',
            'id' => $this->role->id,
            'parentModalId' => 'role-edit-modal',
        ]);

        // draggable-modal 열기
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'role-edit-modal' } 
            }));
        ");
    }

    #[On('admin-roles:role:saved')]
    public function handleRoleSaved(): void
    {
        // 역할이 저장된 후 역할 정보 다시 로드
        if ($this->role) {
            $this->role = $this->roleService->getRoleDetail(['roleId' => $this->role->id]);
        }
    }

    public function resetRole(): void
    {
        $this->role = null;
    }

    public function resetEditForm(): void
    {
        // 자식 모달이 닫힐 때는 아무것도 하지 않음 (부모 모달은 유지)
    }

    public function render()
    {
        return view('livewire.admin.roles.role-detail-modal');
    }
}
