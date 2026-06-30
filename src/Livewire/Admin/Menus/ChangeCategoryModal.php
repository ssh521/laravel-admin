<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Menus;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Livewire\Concerns\AuthorizesMenuEditing;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class ChangeCategoryModal extends Component
{
    use AuthorizesMenuEditing;

    /** @var array<int> */
    public array $selectedMenuIds = [];

    public ?int $categoryId = null; // null = 미분류

    public ?string $modalStackId = null;

    /** @var array<int, array{id:int,name:string,count:int}> */
    public array $categories = [];

    public function mount(array $selectedMenuIds = [], ?string $modalStackId = null): void
    {
        $this->modalStackId = $modalStackId;
        $this->selectedMenuIds = collect($selectedMenuIds)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        $this->loadCategories();
    }

    private function loadCategories(): void
    {
        $this->categories = MenuCategory::active()
            ->ordered()
            ->select(['id', 'name'])
            ->withCount('menus')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'count' => $c->menus_count,
            ])
            ->toArray();
    }

    #[On('admin-menus:change-category-modal:set-selected')]
    public function setSelectedMenuIds($ids): void
    {
        // 객체로 전달된 경우 ids 키에서 배열 추출
        $menuIds = is_array($ids) && isset($ids['ids']) ? $ids['ids'] : $ids;

        // 정수 배열만 허용
        $this->selectedMenuIds = collect($menuIds)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function save(): void
    {
        $this->authorizeMenuEdit();

        if (count($this->selectedMenuIds) === 0) {
            $this->js('alert("변경할 메뉴를 선택해주세요.");');

            return;
        }

        try {
            Menu::whereIn('id', $this->selectedMenuIds)
                ->update(['category_id' => $this->categoryId]);

            if ($this->modalStackId) {
                $this->dispatch('admin:modal-stack:close', id: $this->modalStackId);
            }

            $this->js('setTimeout(() => window.location.reload(), 100);');
        } catch (\Throwable $e) {
            Log::error('ChangeCategoryModal save failed', ['error' => $e->getMessage()]);
            $this->js('alert("카테고리 변경 중 오류가 발생했습니다.");');
        }
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.menus.change-category-modal');
    }
}
