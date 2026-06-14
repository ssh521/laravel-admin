<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Menus;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Models\Menu;

class MenuOrderModal extends Component
{
    public ?int $categoryId = null;

    public ?string $categoryName = null;

    public array $menus = [];

    public bool $isLoading = false;

    public bool $hasChanges = false;

    #[On('admin-menus:menu-order-modal:open')]
    public function open(mixed $data = null): void
    {
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

        // 바로 저장
        $this->saveOrder();
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

            // 성공 알림은 표시하지 않음 (자동 저장이므로)
            // 필요시 알림을 표시하려면 아래 주석 해제
            // $this->dispatch('menu-order-modal:notification', [
            //     'message' => '메뉴 순서가 자동으로 저장되었습니다.',
            //     'type' => 'success',
            // ]);

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
        // 이제는 updateOrder에서 자동으로 저장되므로, 이 메서드는 모달 닫기용으로만 사용
        // 모달 닫기
        $this->close();
    }

    public function close(): void
    {
        $this->resetModal();
        $this->dispatch('close-modal', ['modalId' => 'menu-order-modal']);
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
