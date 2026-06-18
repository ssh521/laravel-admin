<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Services\RoleService;

class RoleForm extends Component
{
    use AuthorizesRequests;

    public $showModal = false;

    public $mode = 'create'; // create | view | edit

    public $parentModalId = null; // 부모 draggable-modal의 ID

    public $roleId;

    public $name = '';

    public $permissionIds = [];

    public $availablePermissions = [];

    private RoleService $roleService;

    public function __construct()
    {
        $this->roleService = app(RoleService::class);
        $this->useAdminGuard();
    }

    public function mount(): void
    {
        $this->useAdminGuard();
        $this->loadAvailablePermissions();
    }

    #[On('admin-roles:role-form:open')]
    public function open(array $payload = []): void
    {
        $this->useAdminGuard();

        $this->resetValidation();
        $this->resetForm();

        $this->mode = $payload['mode'] ?? 'create';
        $roleId = $payload['id'] ?? null;
        $this->parentModalId = $payload['parentModalId'] ?? null;

        if ($this->mode === 'create') {
            $this->authorize('create', Role::class);
        }

        if (in_array($this->mode, ['view', 'edit'], true) && $roleId) {
            $role = $this->roleService->getRoleDetail(['roleId' => $roleId]);
            $this->authorize($this->mode === 'view' ? 'view' : 'update', $role);

            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->permissionIds = $role->permissions->pluck('id')->toArray();
        }

        $this->showModal = true;
    }

    #[On('close-modal')]
    public function handleCloseModal(array $payload = []): void
    {
        // draggable-modal이 닫힐 때도 RoleForm 닫기
        $this->close();
    }

    public function close(): void
    {
        // 이미 닫혀있으면 중복 호출 방지
        if (! $this->showModal) {
            return;
        }

        $this->showModal = false;

        // 자식 모달인 경우 모달 닫기
        if ($this->parentModalId) {
            $this->js("
                window.dispatchEvent(new CustomEvent('close-modal', { 
                    detail: { modalId: '{$this->parentModalId}' } 
                }));
            ");
        }
    }

    public function save(): void
    {
        $this->useAdminGuard();

        if ($this->mode === 'view') {
            return;
        }

        try {
            if ($this->mode === 'create') {
                $this->authorize('create', Role::class);
                // TODO: CreateRoleAction이 필요하면 추가
                // 현재는 update만 구현
            } else {
                $role = Role::findOrFail($this->roleId);
                $this->authorize('update', $role);
                $this->roleService->updateRole([
                    'roleId' => $this->roleId,
                    'name' => $this->name,
                    'permissionIds' => $this->permissionIds ?? [],
                ]);
            }

            $this->dispatch('admin-roles:role:saved');

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
        $this->roleId = null;
        $this->name = '';
        $this->permissionIds = [];
    }

    protected function loadAvailablePermissions(): void
    {
        $this->availablePermissions = Permission::query()
            ->ordered()
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'group' => $this->resolvePermissionGroup($permission->name),
                ];
            })->toArray();
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.roles.role-form');
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    private function resolvePermissionGroup(string $permissionName): string
    {
        $groups = [
            'menu-category-' => 'Menu Category',
            'super-admin-' => 'Super Admin',
            'admin-' => 'Admin Dashboard',
            'role-' => 'Role',
            'permission-' => 'Permission',
            'user-' => 'User',
            'menu-' => 'Menu',
        ];

        foreach ($groups as $prefix => $group) {
            if (str_starts_with($permissionName, $prefix)) {
                return $group;
            }
        }

        return '기타';
    }
}
