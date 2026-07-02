<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Users;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Ssh521\LaravelAdmin\Livewire\Concerns\BuildsUserShowModalPayload;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Services\UserService;

#[Layout('components.admin.layouts.admin')]
#[Title('사용자 관리')]

class UserTable extends Component
{
    use AuthorizesRequests, BuildsUserShowModalPayload, WithPagination;

    private UserService $userService;

    #[Title('사용자 관리')]
    public function __construct()
    {
        $this->userService = app(UserService::class);
        $this->useAdminGuard();
    }

    public $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['name', 'email', 'created_at'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    // 자식 모달창 열기 (사용자 수정)
    public function edit($id)
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('update', $user);

        $this->dispatch('admin:modal-stack:push', [
            'id' => 'user-edit-'.$user->id.'-'.uniqid(),
            'component' => 'admin.users.user-edit-modal',
            'params' => ['userId' => $user->id],
            'title' => '사용자 수정',
            'size' => 'md',
        ]);
    }

    // 사용자 상세 모달 열기
    public function showUserModal(int $userId): void
    {
        $user = $this->userService->getUserDetail(['userId' => $userId]);
        $this->authorize('view', $user);

        $this->dispatch('admin:modal-stack:push', $this->userShowModalPayload($user->id));
    }

    public function delete($id)
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('delete', $user);

        $userName = $user->name;
        $this->userService->deleteUser(['userId' => $id]);

        $this->dispatch('notification:show', message: $userName.' 사용자가 삭제되었습니다.', type: 'success');
        $this->resetPage();
    }

    public function setEmailVerified(int $userId, bool|int|string $isVerified): void
    {
        $user = $this->userService->getUserDetail(['userId' => $userId]);
        $this->authorize('update', $user);

        $updatedUser = $this->userService->setUserEmailVerification([
            'userId' => $userId,
            'isVerified' => $isVerified,
        ]);

        $message = $updatedUser->email_verified_at ? '이메일을 인증 처리했습니다.' : '이메일 인증을 해제했습니다.';
        $this->dispatch('notification:show', message: $message, type: 'success');
    }

    #[On('admin-user-management:user:saved')]
    public function handleUserSaved()
    {
        // 사용자가 저장된 후 목록 새로고침 (현재 페이지 유지)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }

    #[On('user-saved')]
    public function handleUserSavedAlias(): void
    {
        $this->handleUserSaved();
    }

    #[On('admin-user-management:user:rendering')]
    public function handleUserRendering()
    {
        // 사용자 렌더링 이벤트 처리 (필요시 추가 로직 구현)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링
    }

    public function render()
    {
        $this->useAdminGuard();

        // 권한 검사: 사용자 목록 조회 권한이 있는지 확인
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getUserList([
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => 15,
        ]);

        return view('laravel-admin::livewire.admin.users.user-table', [
            'users' => $users,
            'canCreate' => Gate::allows('create', User::class),
            'canEdit' => function ($user) {
                return Gate::allows('update', $user);
            },
            'canDelete' => function ($user) {
                return Gate::allows('delete', $user);
            },
        ]);
    }

    private function useAdminGuard(): void
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        Auth::shouldUse($guard);
        request()->setUserResolver(fn () => Auth::guard($guard)->user());
    }
}
