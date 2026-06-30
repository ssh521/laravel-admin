<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\MenuCategories;

use Livewire\Component;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuCategoryManagerModal extends Component
{
    public ?string $modalStackId = null;

    public string $search = '';

    public function mount(?string $modalStackId = null): void
    {
        $this->modalStackId = $modalStackId;
    }

    public function openRolesModal(int $categoryId): void
    {
        $category = MenuCategory::findOrFail($categoryId);

        $this->dispatch('admin:modal-stack:push', [
            'id' => 'menu-category-roles-'.$category->id.'-'.uniqid(),
            'component' => 'admin.menu-categories.roles-modal',
            'params' => ['menuCategoryId' => $category->id],
            'title' => '['.$category->name.'] 권한 관리',
            'size' => 'lg',
            'width' => 720,
            'height' => 560,
        ]);
    }

    public function render()
    {
        $categories = MenuCategory::query()
            ->withCount('menus')
            ->with('roles')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->ordered()
            ->get();

        return view('laravel-admin::livewire.admin.menu-categories.menu-category-manager-modal', [
            'categories' => $categories,
        ]);
    }
}
