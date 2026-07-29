<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\MenuCategories;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

        $guard = $this->adminGuard();
        $validated = $this->validate([
            'roleIds' => ['array'],
            'roleIds.*' => [
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', $guard),
            ],
        ]);
        $roleIds = Role::query()
            ->where('guard_name', $guard)
            ->whereKey($validated['roleIds'])
            ->pluck('id')
            ->all();

        $this->category->roles()->sync($roleIds);
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
        $this->roleIds = $this->category->roles()
            ->where('roles.guard_name', $this->adminGuard())
            ->pluck('roles.id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    private function loadRoles(): void
    {
        $this->availableRoles = Role::query()
            ->where('guard_name', $this->adminGuard())
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
        $guard = $this->adminGuard();

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }

    private function adminGuard(): string
    {
        return config('laravel-admin.auth.guard', 'laravel_admin');
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.menu-categories.menu-category-roles-modal');
    }
}
