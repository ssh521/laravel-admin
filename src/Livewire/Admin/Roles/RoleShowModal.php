<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\RoleService;

class RoleShowModal extends Component
{
    use AuthorizesRequests;

    public int|string $roleId;

    public ?string $modalStackId = null;

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
    }

    public function openEditModal(): void
    {
        $this->authorize('update', $this->role);

        $this->dispatch('admin:modal-stack:push', [
            'id' => 'role-edit-'.$this->role->id.'-'.uniqid(),
            'component' => 'admin.roles.role-edit-modal',
            'params' => ['roleId' => $this->role->id],
            'title' => '역할 수정',
            'size' => 'lg',
            'width' => 720,
            'height' => 640,
        ]);
    }

    public function editRole(): void
    {
        $this->authorize('update', $this->role);

        $this->redirect(route('admin.roles.edit', $this->role->id));
    }

    #[On('admin-roles:role:saved')]
    public function handleRoleSaved(): void
    {
        $this->loadRole();
    }

    private function loadRole(): void
    {
        $this->role = $this->roleService->getRoleDetail(['roleId' => $this->roleId]);
        $this->authorize('view', $this->role);
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.roles.role-show-modal');
    }
}
