<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Menus;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Livewire\Concerns\AuthorizesMenuEditing;
use Ssh521\LaravelAdmin\Models\Menu;

class MenuOrderModal extends Component
{
    use AuthorizesMenuEditing;

    public ?int $categoryId = null;

    public ?string $categoryName = null;

    public array $menus = [];

    public bool $isLoading = false;

    public bool $hasChanges = false;

    public ?string $modalStackId = null;

    public function mount(?int $categoryId = null, ?string $categoryName = null, ?string $modalStackId = null): void
    {
        $this->modalStackId = $modalStackId;

        if ($categoryId) {
            $this->open([
                'categoryId' => $categoryId,
                'categoryName' => $categoryName,
            ]);
        }
    }

    #[On('admin-menus:menu-order-modal:open')]
    public function open(mixed $data = null): void
    {
        $this->authorizeMenuEdit();

        $this->resetModal();

        $payload = $this->normalizePayload($data);

        // data 객체 안에 있는 경우와 직접 전달된 경우 모두 처리
        $this->categoryId = $payload['data']['categoryId'] ?? $payload['categoryId'] ?? null;
        $this->categoryName = $payload['data']['categoryName'] ?? $payload['categoryName'] ?? null;

        $this->hasChanges = false;
        $this->isLoading = true;
        $this->menus = [];

        if ($this->categoryId) {
            $this->loadMenus();
        }
    }

    private function normalizePayload(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            return (array) $data;
        }

        return [];
    }

    public function loadMenus(): void
    {
        $this->authorizeMenuEdit();

        if (! $this->categoryId) {
            return;
        }

        $this->isLoading = true;

        $this->menus = Menu::where('category_id', $this->categoryId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'sort_order' => $menu->sort_order,
                    'icon' => $menu->icon,
                    'is_active' => $menu->is_active,
                ];
            })
            ->toArray();

        $this->isLoading = false;
    }

    public function updateOrder($newOrder = []): void
    {
        $this->authorizeMenuEdit();

        // newOrder는 [{id: 1, sort_order: 0}, {id: 2, sort_order: 1}, ...] 형태의 배열
        if (! is_array($newOrder) || empty($newOrder) || ! $this->categoryId) {
            return;
        }

        // 메뉴를 ID로 매핑
        $menusById = [];
        foreach ($this->menus as $menu) {
            $menusById[$menu['id']] = $menu;
        }

        // newOrder의 순서대로 메뉴 재정렬
        $reorderedMenus = [];
        foreach ($newOrder as $index => $item) {
            $menuId = isset($item['id']) ? (int) $item['id'] : (int) $item;
            $sortOrder = isset($item['sort_order']) ? (int) $item['sort_order'] : $index;

            if (isset($menusById[$menuId])) {
                $menu = $menusById[$menuId];
                $menu['sort_order'] = $sortOrder;
                $reorderedMenus[] = $menu;
            }
        }

        // 남은 메뉴들 추가 (newOrder에 없는 경우)
        foreach ($this->menus as $menu) {
            $found = false;
            foreach ($newOrder as $item) {
                $menuId = isset($item['id']) ? (int) $item['id'] : (int) $item;
                if ($menuId === $menu['id']) {
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $reorderedMenus[] = $menu;
            }
        }

        $this->menus = $reorderedMenus;

        // 이벤트로 전달된 순서를 저장
        $this->saveOrder();
    }

    #[On('admin-menus:menu-order-modal:update-order')]
    public function updateOrderFromEvent(mixed $data = null): void
    {
        $this->authorizeMenuEdit();

        $payload = $this->normalizePayload($data);
        $eventCategoryId = $payload['data']['categoryId'] ?? $payload['categoryId'] ?? null;
        $newOrder = $payload['data']['newOrder'] ?? $payload['newOrder'] ?? [];

        if (! $this->categoryId || (int) $eventCategoryId !== $this->categoryId || ! is_array($newOrder) || empty($newOrder)) {
            $this->dispatch('admin-menus:menu-order-modal:notification', [
                'message' => '메뉴 순서 저장 요청이 올바르지 않습니다.',
                'type' => 'error',
            ]);

            return;
        }

        $this->updateOrder($newOrder);
    }

    private function saveOrder(): void
    {
        if (! $this->categoryId || empty($this->menus)) {
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($this->menus as $index => $menu) {
                Menu::where('id', $menu['id'])
                    ->where('category_id', $this->categoryId)
                    ->update(['sort_order' => $index]);
            }

            DB::commit();

            $this->hasChanges = false;

            // 왼쪽 메뉴 새로고침을 위한 이벤트 발생
            $this->dispatch('admin-menus:menu-order:updated');

            $this->dispatch('admin-menus:menu-order-modal:notification', [
                'message' => '메뉴 순서가 저장되었습니다.',
                'type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu order update failed', [
                'error' => $e->getMessage(),
                'category_id' => $this->categoryId,
                'menus' => $this->menus,
            ]);

            $this->dispatch('admin-menus:menu-order-modal:notification', [
                'message' => '저장 중 오류가 발생했습니다.',
                'type' => 'error',
            ]);
        }
    }

    public function save(): void
    {
        $this->close();
    }

    public function close(): void
    {
        $this->resetModal();

        if ($this->modalStackId) {
            $this->dispatch('admin:modal-stack:close', id: $this->modalStackId);
        }
    }

    public function resetModal(): void
    {
        $this->categoryId = null;
        $this->categoryName = null;
        $this->menus = [];
        $this->isLoading = false;
        $this->hasChanges = false;
    }

    public function updatedMenus(): void
    {
        // 메뉴가 업데이트되면 다시 로드하지 않음 (드래그 앤 드롭으로 이미 업데이트됨)
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.menus.menu-order-modal');
    }
}
