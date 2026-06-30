<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

class UserSearchModal extends Component
{
    use AuthorizesRequests,  WithPagination;

    public string $modalId = '';

    public ?string $modalStackId = null;

    public ?string $userId = null;

    public string $search = '';

    public int $perPage = 10;

    private ResolvesUserModel $users;

    public function boot(ResolvesUserModel $users): void
    {
        $this->users = $users;
    }

    public function mount(?string $modalStackId = null, ?string $userId = null, ?string $userName = null, ?string $userEmail = null): void
    {
        $this->modalStackId = $modalStackId;
        $this->applyInput($userId, $userName, $userEmail);
    }

    #[On('admin-user-management:user-search-modal:prefill')]
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

    #[On('admin-user-management:user-search-modal:load-info')]
    public function loadUser(string $userId): void
    {
        $this->userId = $userId;
    }

    public function selectUser(string $userId): void
    {
        $user = $this->users->findOrFail((int) $userId);

        $this->js("
            window.dispatchEvent(new CustomEvent('user-selected', {
                detail: ".json_encode([
            'userId' => $user->id,
            'userName' => $user->name,
            'userEmail' => $user->email,
        ])."
            }));
            
        ");

        if ($this->modalStackId) {
            $this->dispatch('admin:modal-stack:close', id: $this->modalStackId);
        }
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->userId = null;
    }

    public function openUserEditModal(string $userId): void
    {
        $this->dispatch('admin:modal-stack:push', [
            'id' => 'user-edit-'.$userId.'-'.uniqid(),
            'component' => 'admin.users.user-edit-modal',
            'params' => ['userId' => $userId],
            'title' => '사용자 수정',
            'size' => 'md',
            'width' => 576,
            'height' => 560,
        ]);
    }

    public function openUserViewModal(string $userId): void
    {
        $this->dispatch('admin:modal-stack:push', [
            'id' => 'user-show-'.$userId.'-'.uniqid(),
            'component' => 'admin.users.user-show-modal',
            'params' => ['userId' => $userId],
            'title' => '사용자 상세 정보',
            'size' => 'lg',
            'width' => 768,
            'height' => 560,
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
        $users = $this->users->query()
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

        return view('laravel-admin::livewire.admin.users.user-search-modal', [
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
        ]);

    }

    #[On('admin-user-management:user:saved')]
    public function handleUserSaved()
    {
        // 사용자가 저장된 후 목록 새로고침 (현재 페이지 유지)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }
}
