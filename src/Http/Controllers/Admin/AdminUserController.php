<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Services\AdminUserService;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserService $adminUserService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('manageUsers');

        $adminUsers = $this->adminUserService->getAdminUserList([
            'search' => $request->query('search'),
            'role' => $request->query('role'),
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
            'perPage' => 15,
        ]);

        $roles = $this->getAvailableRoles();

        return view('admin.admin-users.index', compact('adminUsers', 'roles'));
    }

    public function create(): View
    {
        Gate::authorize('manageUsers');

        $roles = $this->getAvailableRoles();

        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manageUsers');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'same:confirm-password'],
            'confirm-password' => ['required', 'string', 'min:8'],
            'email_verified' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer'],
        ]);

        $adminUser = $this->adminUserService->createAdminUser([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'isVerified' => $request->boolean('email_verified'),
        ]);

        $this->adminUserService->assignRolesToAdminUser([
            'adminUserId' => $adminUser->getKey(),
            'roleIds' => $validated['roles'] ?? [],
        ]);

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Admin user created successfully.');
    }

    public function show(int $adminUser): View
    {
        Gate::authorize('manageUsers');

        $adminUser = $this->adminUserService->getAdminUserDetail([
            'adminUserId' => $adminUser,
        ]);

        return view('admin.admin-users.show', compact('adminUser'));
    }

    public function edit(int $adminUser): View
    {
        Gate::authorize('manageUsers');

        $adminUser = $this->adminUserService->getAdminUserDetail([
            'adminUserId' => $adminUser,
        ]);
        $roles = $this->getAvailableRoles();

        return view('admin.admin-users.edit', compact('adminUser', 'roles'));
    }

    public function profile(): View
    {
        $adminUserId = Auth::guard(config('laravel-admin.auth.guard', 'laravel_admin'))->id();

        $adminUser = $this->adminUserService->getAdminUserDetail([
            'adminUserId' => $adminUserId,
        ]);
        $roles = $this->getAvailableRoles();
        $isProfile = true;

        return view('admin.admin-users.edit', compact('adminUser', 'roles', 'isProfile'));
    }

    public function profileUpdate(Request $request): RedirectResponse
    {
        $adminUserId = Auth::guard(config('laravel-admin.auth.guard', 'laravel_admin'))->id();
        $adminUser = $this->adminUserService->getAdminUserDetail([
            'adminUserId' => $adminUserId,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'same:confirm-password'],
            'confirm-password' => ['nullable', 'string', 'min:8'],
        ]);

        $updateData = [
            'adminUserId' => $adminUserId,
            'name' => $validated['name'],
            'email' => $adminUser->email,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $this->adminUserService->updateAdminUser($updateData);

        return redirect()
            ->route(config('laravel-admin.route_name_prefix', 'admin.').'profile.show')
            ->with('success', '프로필이 성공적으로 수정되었습니다.');
    }

    public function update(Request $request, int $adminUser): RedirectResponse
    {
        Gate::authorize('manageUsers');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'same:confirm-password'],
            'confirm-password' => ['nullable', 'string', 'min:8'],
            'email_verified' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer'],
        ]);

        $updateData = [
            'adminUserId' => $adminUser,
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $adminUserModel = $this->adminUserService->updateAdminUser($updateData);

        $this->adminUserService->setAdminUserEmailVerification([
            'adminUserId' => $adminUserModel->getKey(),
            'isVerified' => $request->boolean('email_verified'),
        ]);

        $this->adminUserService->assignRolesToAdminUser([
            'adminUserId' => $adminUserModel->getKey(),
            'roleIds' => $validated['roles'] ?? [],
        ]);

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    public function destroy(int $adminUser): RedirectResponse
    {
        Gate::authorize('manageUsers');

        if ((int) Auth::guard(config('laravel-admin.auth.guard', 'laravel_admin'))->id() === $adminUser) {
            return redirect()
                ->route('admin.admin-users.index')
                ->with('error', 'You cannot delete the currently authenticated admin user.');
        }

        $this->adminUserService->deleteAdminUser([
            'adminUserId' => $adminUser,
        ]);

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Admin user deleted successfully.');
    }

    private function getAvailableRoles()
    {
        return Role::query()
            ->where('guard_name', config('laravel-admin.auth.guard', 'laravel_admin'))
            ->orderBy('name')
            ->get();
    }
}
