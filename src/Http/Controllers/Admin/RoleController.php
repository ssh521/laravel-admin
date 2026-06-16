<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\Permission;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // authorizeResource 대신 개별 메서드에서 authorize 호출
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $data = Role::query()
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

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validatedData['name']]);

        // 권한 동기화 - 권한이 선택되지 않았을 때도 빈 배열로 처리
        if (isset($validatedData['permissions']) && ! empty($validatedData['permissions'])) {
            $permissions = Permission::whereIn('id', $validatedData['permissions'])->pluck('name');
            $role->syncPermissions($permissions);
        } else {
            // 권한이 선택되지 않았을 때는 모든 권한을 제거
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role added successfully');
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load('permissions');

        return view('laravel-admin::admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = $this->getOrderedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('laravel-admin::admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validatedData['name']]);

        // 권한 동기화 - 권한이 선택되지 않았을 때도 빈 배열로 처리
        if (isset($validatedData['permissions']) && ! empty($validatedData['permissions'])) {
            $permissions = Permission::whereIn('id', $validatedData['permissions'])->pluck('name');
            $role->syncPermissions($permissions);
        } else {
            // 권한이 선택되지 않았을 때는 모든 권한을 제거
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
    }

    private function getOrderedPermissions()
    {
        return Permission::query()
            ->ordered()
            ->get();
    }
}
