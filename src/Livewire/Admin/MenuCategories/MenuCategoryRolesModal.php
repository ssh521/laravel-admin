<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\MenuCategories;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuCategoryRolesModal extends Component
{
    use AuthorizesRequests;

    public int|string $menuCategoryId;

    public ?string $modalStackId = null;

    public MenuCategory $category;

    public array $roleIds = [];

    public array $availableRoles = [];

    public function mount(int|string $menuCategoryId, ?string $modalStackId = null): void
    {
        $this->menuCategoryId = $menuCategoryId;
        $this->modalStackId = $modalStackId;
        $this->loadCategory();
        $this->loadRoles();
    }

    public function save(): void
    {
        $this->useAdminGuard();
        $this->authorize('update', $this->category);

        $this->category->roles()->sync($this->roleIds);
        $this->category->load('roles');

        $this->dispatch('admin-menu-categories:menu-categories:reordered');
        $this->dispatch('notification:show', message: '권한이 성공적으로 저장되었습니다.', type: 'success');

        if ($this->modalStackId) {
            $this->dispatch('admin:modal-stack:close', id: $this->modalStackId);
        }
    }

    private function loadCategory(): void
    {
        $this->useAdminGuard();
        $this->category = MenuCategory::with('roles')->findOrFail($this->menuCategoryId);
        $this->authorize('update', $this->category);
        $this->roleIds = $this->category->roles->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    private function loadRoles(): void
    {
        $this->availableRoles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
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
        return view('laravel-admin::livewire.admin.menu-categories.menu-category-roles-modal');
    }
}
