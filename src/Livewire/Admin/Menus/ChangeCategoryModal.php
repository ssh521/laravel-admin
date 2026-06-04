<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Menus;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class ChangeCategoryModal extends Component
{
    /** @var array<int> */
    public array $selectedMenuIds = [];

    public ?int $categoryId = null; // null = 미분류

    /** @var array<int, array{id:int,name:string,count:int}> */
    public array $categories = [];

    public function mount(): void
    {
        $this->loadCategories();
    }

    private function loadCategories(): void
    {
        $this->categories = MenuCategory::active()
            ->ordered()
            ->get(['id', 'name'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'count' => $c->menus()->count(),
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
        if (count($this->selectedMenuIds) === 0) {
            $this->js('alert("변경할 메뉴를 선택해주세요.");');

            return;
        }

        try {
            Menu::whereIn('id', $this->selectedMenuIds)
                ->update(['category_id' => $this->categoryId]);

            $this->js('
                window.dispatchEvent(new CustomEvent("draggable-modal-close", {
                    detail: { modalId: "category-selection-modal" }
                }));
                setTimeout(() => window.location.reload(), 100);
            ');
        } catch (\Throwable $e) {
            Log::error('ChangeCategoryModal save failed', ['error' => $e->getMessage()]);
            $this->js('alert("카테고리 변경 중 오류가 발생했습니다.");');
        }
    }

    public function render()
    {
        return view('livewire.admin.menus.change-category-modal');
    }
}
