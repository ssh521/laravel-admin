<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Services\UserService;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function selectUser()
    {
        return view('admin.users.select-user');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $data = $this->userService->getUserList([
            'search' => $request->search,
            'role' => $request->role,
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
            'perPage' => 15,
        ]);

        $roles = $this->getAvailableRoles();

        return view('admin.users.index', compact('data', 'roles'));
    }

    public function list()
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = $this->getAvailableRoles();
        $permissions = Permission::all();

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|same:confirm-password',
            'confirm-password' => 'required|string|min:8',
            'roles' => 'required|array|min:1',
        ]);

        $user = $this->userService->createUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // 이메일 인증 상태 처리
        $user->email_verified_at = $request->has('email_verified') ? now() : null;
        $user->save();

        // 역할 할당
        if ($request->roles) {
            $this->userService->assignRolesToUser([
                'userId' => $user->id,
                'roleIds' => $request->roles,
            ]);
        }

        // 권한 할당
        $this->syncUserPermissions($user, $request->permissions);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user = $this->userService->getUserDetail([
            'userId' => $user->id,
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = $this->getAvailableRoles();
        $permissions = Permission::orderBy('name', 'asc')->get();

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'same:confirm-password',
        ]);

        $updateData = [
            'userId' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (! empty($request->password)) {
            $updateData['password'] = $request->password;
        }

        $user = $this->userService->updateUser($updateData);

        // 이메일 인증 상태 처리
        $user->email_verified_at = $request->has('email_verified') ? now() : null;
        $user->save();

        // 역할 할당
        if (isset($request->roles)) {
            $this->userService->assignRolesToUser([
                'userId' => $user->id,
                'roleIds' => $request->roles,
            ]);
        } else {
            $user->roles()->detach();
        }

        // 권한 할당
        $this->syncUserPermissions($user, $request->permissions);

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' 정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->deleteUser([
            'userId' => $user->id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
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

    /**
     * Sync user permissions
     */
    private function syncUserPermissions(User $user, $permissions)
    {
        if (isset($permissions)) {
            $user->permissions()->sync($permissions);
        } else {
            $user->permissions()->detach();
        }
    }
}
