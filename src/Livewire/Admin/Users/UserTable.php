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
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Services\UserService;

#[Layout('components.admin.layouts.admin')]
#[Title('사용자 관리')]

class UserTable extends Component
{
    use AuthorizesRequests, WithPagination;

    private UserService $userService;

    #[Title('사용자 관리')]
    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public $search = '';

    public $role = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    // 자식 모달창 열기 (사용자 생성)
    public function create()
    {
        $this->authorize('create', User::class);
        $this->dispatch('admin-users:user-form:open', ['mode' => 'create']);
    }

    // 자식 모달창 열기 (사용자 수정)
    public function edit($id)
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('update', $user);
        $this->dispatch('admin-users:user-form:open', ['mode' => 'edit', 'userId' => $user->id]);
    }

    // 사용자 상세 모달 열기
    public function showUserDetailModal(int $userId): void
    {
        $this->dispatch('admin-users:user-detail-modal:open', [
            'userId' => $userId,
        ]);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
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

    #[On('admin-users:user:saved')]
    public function handleUserSaved()
    {
        // 사용자가 저장된 후 목록 새로고침 (현재 페이지 유지)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }

    #[On('admin-users:user:rendering')]
    public function handleUserRendering()
    {
        // 사용자 렌더링 이벤트 처리 (필요시 추가 로직 구현)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링
    }

    #[On('admin-users:user-roles:updated')]
    public function handleUserRolesUpdated()
    {
        // 사용자 역할이 업데이트된 후 목록 새로고침
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }

    public function manageRoles($id)
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('assignRoles', $user);
        $this->dispatch('admin-users:user-role-form:open', ['id' => $user->id, 'mode' => 'edit']);
    }

    public function viewRoles($id)
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('assignRoles', $user);
        $this->dispatch('admin-users:user-role-form:open', ['id' => $user->id, 'mode' => 'view']);
    }

    public function render()
    {
        // 권한 검사: 사용자 목록 조회 권한이 있는지 확인
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getUserList([
            'search' => $this->search,
            'role' => $this->role,
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
            'perPage' => 15,
        ]);

        $roles = $this->getAvailableRoles();

        return view('livewire.admin.users.user-table', [
            'users' => $users,
            'roles' => $roles,
            'canCreate' => Gate::allows('create', User::class),
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

    /**
     * Get available roles based on current user's permissions
     */
    private function getAvailableRoles()
    {
        $currentUser = Auth::user();
        if ($currentUser->roles->contains('name', 'Super Admin')) {
            return Role::all();
        } else {
            return Role::where('name', '!=', 'Super Admin')->get();
        }
    }
}
