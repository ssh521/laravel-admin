<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Services\RoleService;

class RoleEditModal extends Component
{
    use AuthorizesRequests;

    public int|string $roleId;

    public ?string $modalStackId = null;

    public string $name = '';

    public array $permissionIds = [];

    #[Locked]
    public array $originalPermissionIds = [];

    public array $availablePermissions = [];

    public object $role;

    private RoleService $roleService;

    public function boot(RoleService $roleService): void
    {
        $this->roleService = $roleService;
        $this->useAdminGuard();
    }

    public function mount(int|string $roleId, ?string $modalStackId = null): void
    {
        $this->roleId = $roleId;
        $this->modalStackId = $modalStackId;
        $this->loadRole();
        $this->loadAvailablePermissions();
    }

    public function save(): void
    {
        $this->useAdminGuard();

        try {
            $role = $this->roleService->getRoleDetail(['roleId' => $this->roleId]);
            $this->authorize('update', $role);

            $canAssignPermissions = Gate::allows('assignPermissions', $role);

            if (! $canAssignPermissions && $this->normalizedPermissionIds($this->permissionIds) !== $this->normalizedPermissionIds($this->originalPermissionIds)) {
                $this->authorize('assignPermissions', $role);
            }

            $this->role = $this->roleService->updateRole([
                'roleId' => $this->roleId,
                'name' => $this->name,
                'permissionIds' => $canAssignPermissions ? $this->permissionIds : null,
            ]);

            $this->permissionIds = $this->role->permissions->pluck('id')->map(fn ($id) => (string) $id)->all();
            $this->originalPermissionIds = $this->permissionIds;

            $this->dispatch('admin-roles:role:saved');
            $this->dispatch('notification:show', message: '역할이 저장되었습니다.', type: 'success');

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

    private function loadRole(): void
    {
        $this->role = $this->roleService->getRoleDetail(['roleId' => $this->roleId]);
        $this->authorize('update', $this->role);
        $this->name = (string) $this->role->name;
        $this->permissionIds = $this->role->permissions->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->originalPermissionIds = $this->permissionIds;
    }

    /**
     * @param  array<int, int|string>  $permissionIds
     * @return array<int, int>
     */
    private function normalizedPermissionIds(array $permissionIds): array
    {
        $ids = array_values(array_unique(array_map(intval(...), $permissionIds)));
        sort($ids);

        return $ids;
    }

    private function loadAvailablePermissions(): void
    {
        $this->availablePermissions = Permission::query()
            ->where('guard_name', config('laravel-admin.auth.guard', 'laravel_admin'))
            ->ordered()
            ->get()
            ->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'canAssign' => Gate::allows('assignToRoles', $permission),
            ])
            ->toArray();
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.roles.role-edit-modal', [
            'canAssignPermissions' => Gate::allows('assignPermissions', $this->role),
        ]);
    }
}
