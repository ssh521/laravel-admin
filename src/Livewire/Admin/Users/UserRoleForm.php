<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Services\UserService;

class UserRoleForm extends Component
{
    use AuthorizesRequests;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public $user;

    public $userRoles = [];

    public $availableRoles = [];

    public $selectedRoles = [];

    public $mode = 'view'; // 'view', 'edit'

    public $showModal = false;

    public $parentModalId = null; // 부모 draggable-modal의 ID

    public function mount()
    {
        $this->loadRoles();
    }

    #[On('admin-users:user-role-form:open')]
    public function open(array $payload = []): void
    {
        $this->resetValidation();
        $this->resetForm();

        $this->mode = $payload['mode'] ?? 'view';
        $userId = $payload['id'] ?? null;
        // 이벤트에서 전달된 parentModalId가 있으면 사용, 없으면 초기 속성 유지
        $this->parentModalId = $payload['parentModalId'] ?? $this->parentModalId;

        if (! $userId) {
            return;
        }

        $this->user = $this->userService->getUserDetail(['userId' => $userId]);
        $this->authorize('assignRolesView', $this->user);

        $this->loadUserRoles();
        $this->loadAvailableRoles();
        $this->showModal = true;
    }

    #[On('close-modal')]
    public function handleCloseModal(array $payload = []): void
    {
        // draggable-modal이 닫힐 때도 UserRoleForm 닫기
        $this->close();
    }

    public function close(): void
    {
        // 이미 닫혀있으면 중복 호출 방지
        if (! $this->showModal) {
            return;
        }

        $this->showModal = false;
        $this->resetForm();

        // draggable-modal이 있는 경우 모달 닫기 이벤트 디스패치
        // UserRoleForm이 draggable-modal 안에 있을 때 모달도 함께 닫기
        if ($this->parentModalId) {
            $this->js("
                window.dispatchEvent(new CustomEvent('close-modal', { 
                    detail: { modalId: '{$this->parentModalId}' } 
                }));
            ");
        }
    }

    protected function resetForm(): void
    {
        $this->user = null;
        $this->userRoles = [];
        $this->selectedRoles = [];
        $this->mode = 'view';
    }

    public function loadUserRoles()
    {
        if ($this->user) {
            $this->userRoles = $this->user->roles->pluck('id')->toArray();
            $this->selectedRoles = $this->userRoles;
        }
    }

    public function loadRoles()
    {
        // 현재 사용자가 볼 수 있는 역할들만 로드
        $this->availableRoles = Role::all()->filter(function ($role) {
            return Gate::allows('view', $role);
        })->values();
    }

    public function loadAvailableRoles()
    {
        // 사용자에게 할당 가능한 역할들만 로드
        $this->availableRoles = Role::all()->filter(function ($role) {
            return Gate::allows('view', $role);
        })->values();
    }

    public function toggleRole($roleId)
    {
        if (in_array($roleId, $this->selectedRoles)) {
            $this->selectedRoles = array_filter($this->selectedRoles, function ($id) use ($roleId) {
                return $id != $roleId;
            });
        } else {
            $this->selectedRoles[] = $roleId;
        }
    }

    public function save()
    {
        if (! $this->user) {
            return;
        }

        // 권한 검사
        $this->authorize('assignRoles', $this->user);

        // 선택된 역할들을 사용자에게 할당
        $selectedRoleIds = array_values($this->selectedRoles);
        $this->userService->assignRolesToUser([
            'userId' => $this->user->id,
            'roleIds' => $selectedRoleIds,
        ]);

        // 성공 메시지와 함께 이벤트 발생
        session()->flash('message', '사용자 역할이 성공적으로 업데이트되었습니다.');

        $this->dispatch('admin-users:user-roles:updated', ['userId' => $this->user->id]);

        // 자식 모달인 경우 모달 닫기
        if ($this->parentModalId) {
            $this->js("
                window.dispatchEvent(new CustomEvent('close-modal', { 
                    detail: { modalId: '{$this->parentModalId}' } 
                }));
            ");
        }

        $this->close();
    }

    public function cancel(): void
    {
        $this->close();
    }

    public function render()
    {
        return view('livewire.admin.users.user-role-form');
    }
}
