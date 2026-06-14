<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
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
        $this->useAdminGuard();
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    #[On('admin-roles:role-detail-modal:show')]
    public function showRoleDetail(mixed $payload = null, mixed $roleId = null): void
    {
        $this->useAdminGuard();

        $payload = $this->normalizePayload($payload);
        $roleId = $payload['roleId'] ?? $roleId ?? $payload[0] ?? null;

        if (! $roleId) {
            return;
        }

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

    private function normalizePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (is_object($payload)) {
            return (array) $payload;
        }

        if ($payload) {
            return [$payload];
        }

        return [];
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
        return view('laravel-admin::livewire.admin.roles.role-detail-modal');
    }
}
