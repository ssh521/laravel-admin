<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Ssh521\LaravelAdmin\Models\User;

class UserSearchModal extends Component
{
    use AuthorizesRequests,  WithPagination;

    public string $modalId = '';

    public ?string $userId = null;

    public string $search = '';

    public int $perPage = 10;

    #[On('admin-users:user-search-modal:prefill')]
    public function setUserSearchForm(mixed $data = null): void
    {
        $this->resetSearchState();

        $payload = $this->normalizePayload($data);
        $this->applyInput(
            $payload['userId'] ?? null,
            $payload['userName'] ?? null,
            $payload['userEmail'] ?? null,
        );

        $this->resetPage();
    }

    #[On('admin-users:user-search-modal:load-info')]
    public function loadUser(string $userId): void
    {
        $this->userId = $userId;
    }

    public function selectUser(string $userId): void
    {
        $user = User::findOrFail($userId);

        $this->js("
            window.dispatchEvent(new CustomEvent('user-selected', {
                detail: ".json_encode([
            'userId' => $user->id,
            'userName' => $user->name,
            'userEmail' => $user->email,
        ])."
            }));
            
            // 모달 닫기 이벤트 디스패치
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: { modalId: '{$this->modalId}' }
            }));
        ");
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->userId = null;
    }

    public function openUserEditModal(string $userId): void
    {
        $this->dispatch('admin-users:user-form:open', [
            'mode' => 'edit',
            'userId' => $userId,
        ]);
    }

    public function openUserViewModal(string $userId): void
    {
        $this->dispatch('admin-users:user-form:open', [
            'mode' => 'view',
            'userId' => $userId,
        ]);
    }

    public function openUserRoleViewModal(string $userId): void
    {
        $this->dispatch('admin-users:user-role-form:open', [
            'mode' => 'view',
            'id' => $userId,
        ]);
    }

    private function resetSearchState(): void
    {
        $this->search = '';
        $this->userId = null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    private function normalizePayload(mixed $data): array
    {
        return is_array($data) ? $data : (is_object($data) ? (array) $data : []);
    }

    private function applyInput(?string $userId, ?string $userName, ?string $userEmail): void
    {
        $trimName = is_string($userName) ? trim($userName) : '';
        $trimEmail = is_string($userEmail) ? trim($userEmail) : '';
        $strUserId = isset($userId) ? (string) $userId : '';

        if ($trimName !== '') {
            $this->search = $trimName;

            return;
        }

        if ($trimEmail !== '') {
            $this->search = $trimEmail;

            return;
        }

        if ($strUserId !== '') {
            $this->userId = $strUserId;
        }
    }

    public function render(): View
    {
        $users = User::query()
            ->when($this->userId, function ($q) {
                $q->where('id', $this->userId);
            })
            ->when(! $this->userId && $this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->paginate($this->perPage);

        return view('livewire.admin.users.user-search-modal', [
            'users' => $users,
            'canCreate' => Gate::allows('create', User::class),
            'canView' => function ($user) {
                return Gate::allows('view', $user);
            },
            'canEdit' => function ($user) {
                return Gate::allows('update', $user);
            },
            'canDelete' => function ($user) {
                return Gate::allows('delete', $user);
            },
            'canAssignRoles' => function ($user) {
                return Gate::allows('assignRoles', $user);
            },
        ]);

    }

    #[On('admin-users:user:saved')]
    public function handleUserSaved()
    {
        // 사용자가 저장된 후 목록 새로고침 (현재 페이지 유지)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }
}
