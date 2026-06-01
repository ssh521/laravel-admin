<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\MenuCategories;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuCategoryList extends Component
{
    use AuthorizesRequests, WithPagination;

    public $search = '';

    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        // 권한 검사: 메뉴 카테고리 목록 조회 권한이 있는지 확인
        $this->authorize('viewAny', MenuCategory::class);

        $categories = MenuCategory::query()
            ->withCount('menus')
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->perPage);

        return view('livewire.admin.menu-categories.menu-category-list', [
            'categories' => $categories,
            'canCreate' => Gate::allows('create', MenuCategory::class),
            'canView' => function ($category) {
                return Gate::allows('view', $category);
            },
            'canUpdate' => function ($category) {
                return Gate::allows('update', $category);
            },
            'canDelete' => function ($category) {
                return Gate::allows('delete', $category);
            },
        ]);
    }

    #[On('admin-menu-categories:menu-category:saved')]
    public function handleMenuCategorySaved(): void
    {
        // 메뉴 카테고리가 저장된 후 목록 새로고침
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }

    #[On('admin-menu-categories:menu-categories:reordered')]
    public function handleMenuCategoriesReordered(): void
    {
        // 메뉴 카테고리 순서가 변경된 후 목록 새로고침
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }
}
