<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class RoleModal extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;

    public ?int $categoryId = null;

    public ?string $categoryName = null;

    public array $availableRoles = [];

    public array $selectedRoles = [];

    public bool $isLoading = false;

    #[On('admin-roles:role-modal:open')]
    public function open(mixed $data = null): void
    {
        $payload = $this->normalizePayload($data);

        // data 객체 안에 있는 경우와 직접 전달된 경우 모두 처리
        $categoryId = $payload['data']['categoryId'] ?? $payload['categoryId'] ?? null;
        $categoryName = $payload['data']['categoryName'] ?? $payload['categoryName'] ?? null;

        if (! $categoryId) {
            return;
        }

        $category = MenuCategory::find($categoryId);
        if (! $category) {
            return;
        }

        $this->authorize('update', $category);

        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->isLoading = true;
        $this->showModal = true;

        $this->loadRoles();
    }

    #[On('close-modal')]
    public function handleCloseModal(mixed $data = null): void
    {
        $payload = $this->normalizePayload($data);

        // draggable-modal이 닫힐 때도 RoleModal 닫기
        if (isset($payload['modalId']) && $payload['modalId'] === 'roles-modal') {
            $this->close();
        }
    }

    public function loadRoles(): void
    {
        if (! $this->categoryId) {
            return;
        }

        $category = MenuCategory::with('roles')->find($this->categoryId);
        if (! $category) {
            return;
        }

        $this->availableRoles = $this->getAvailableRoles()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            })
            ->toArray();

        $this->selectedRoles = $category->roles->pluck('id')->toArray();
        $this->isLoading = false;
    }

    public function save(): void
    {
        if (! $this->categoryId) {
            return;
        }

        $category = MenuCategory::find($this->categoryId);
        if (! $category) {
            return;
        }

        $this->authorize('update', $category);

        $category->roles()->sync($this->selectedRoles);

        $this->close();

        // 페이지 새로고침
        $this->js('
            setTimeout(() => {
                window.location.reload();
            }, 100);
        ');
    }

    public function close(): void
    {
        if (! $this->showModal) {
            return;
        }

        $this->showModal = false;
        $this->categoryId = null;
        $this->categoryName = null;
        $this->availableRoles = [];
        $this->selectedRoles = [];

        $this->js("
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: { modalId: 'roles-modal' }
            }));
        ");
    }

    private function normalizePayload(mixed $data): array
    {
        return is_array($data) ? $data : (is_object($data) ? (array) $data : []);
    }

    private function getAvailableRoles()
    {
        $currentUser = Auth::user();
        if ($currentUser->roles->contains('name', 'Super Admin')) {
            return Role::all();
        }

        return Role::where('name', '!=', 'Super Admin')->get();
    }

    public function render()
    {
        return view('livewire.admin.roles.role-modal');
    }
}
