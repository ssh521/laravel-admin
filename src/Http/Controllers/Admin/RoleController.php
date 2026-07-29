<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Support\AdminBroadcastNotifier;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly AdminBroadcastNotifier $broadcastNotifier)
    {
        // authorizeResource 대신 개별 메서드에서 authorize 호출
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $data = Role::query()
            ->where('guard_name', $this->adminGuard())
            ->when(
                $request->search,
                function (Builder $builder) use ($request) {
                    $builder->where('name', 'like', "%{$request->search}%");
                }
            )->latest()->paginate(15);

        return view('laravel-admin::admin.roles.index', compact('data'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions = $this->getOrderedPermissions();

        return view('laravel-admin::admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $guard = $this->adminGuard();
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where('guard_name', $guard),
            ],
            'permissions' => 'array',
            'permissions.*' => [
                Rule::exists('permissions', 'id')->where('guard_name', $guard),
            ],
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'guard_name' => $guard,
        ]);

        // 권한 동기화 - 권한이 선택되지 않았을 때도 빈 배열로 처리
        if (isset($validatedData['permissions']) && ! empty($validatedData['permissions'])) {
            $permissions = Permission::query()
                ->where('guard_name', $guard)
                ->whereIn('id', $validatedData['permissions'])
                ->get();
            $role->syncPermissions($permissions);
        } else {
            // 권한이 선택되지 않았을 때는 모든 권한을 제거
            $role->syncPermissions([]);
        }

        $this->broadcastNotifier->resourceChanged(
            resource: $role->load('permissions'),
            resourceType: 'role',
            event: 'created',
            title: '관리자 역할이 생성되었습니다',
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role added successfully');
    }

    public function show(Role $role)
    {
        $this->ensureAdminGuard($role);
        $this->authorize('view', $role);

        $role->load('permissions');

        return view('laravel-admin::admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->ensureAdminGuard($role);
        $this->authorize('update', $role);

        $permissions = $this->getOrderedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('laravel-admin::admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->ensureAdminGuard($role);
        $this->authorize('update', $role);

        $guard = $this->adminGuard();
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $guard)
                    ->ignore($role->id),
            ],
            'permissions' => 'array',
            'permissions.*' => [
                Rule::exists('permissions', 'id')->where('guard_name', $guard),
            ],
        ]);

        $role->update(['name' => $validatedData['name']]);

        // 권한 동기화 - 권한이 선택되지 않았을 때도 빈 배열로 처리
        if (isset($validatedData['permissions']) && ! empty($validatedData['permissions'])) {
            $permissions = Permission::query()
                ->where('guard_name', $guard)
                ->whereIn('id', $validatedData['permissions'])
                ->get();
            $role->syncPermissions($permissions);
        } else {
            // 권한이 선택되지 않았을 때는 모든 권한을 제거
            $role->syncPermissions([]);
        }

        $this->broadcastNotifier->resourceChanged(
            resource: $role->load('permissions'),
            resourceType: 'role',
            event: 'updated',
            title: '관리자 역할이 수정되었습니다',
            type: 'warning',
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $this->ensureAdminGuard($role);
        $this->authorize('delete', $role);

        $this->broadcastNotifier->resourceChanged(
            resource: $role,
            resourceType: 'role',
            event: 'deleted',
            title: '관리자 역할이 삭제되었습니다',
            type: 'warning',
            includeUrl: false,
        );

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
    }

    private function getOrderedPermissions()
    {
        return Permission::query()
            ->where('guard_name', $this->adminGuard())
            ->ordered()
            ->get();
    }

    private function adminGuard(): string
    {
        return config('laravel-admin.auth.guard', 'laravel_admin');
    }

    private function ensureAdminGuard(Role $role): void
    {
        abort_unless($role->guard_name === $this->adminGuard(), 404);
    }
}
