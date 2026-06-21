<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Support\AdminBroadcastNotifier;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly AdminBroadcastNotifier $broadcastNotifier)
    {
        // authorizeResource 대신 개별 메서드에서 authorize 호출
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        // 허용된 정렬 필드만 사용
        $allowedSorts = ['name', 'description', 'created_at'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        // 허용된 정렬 방향만 사용
        if (! in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $data = Permission::query()
            ->when(
                $request->search,
                function (Builder $builder) use ($request) {
                    $builder->where('name', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                }
            )
            ->orderBy($sort, $direction)
            ->orderBy('sort_order', 'asc') // sort_order를 보조 정렬로 추가
            ->paginate(15);

        return view('laravel-admin::admin.permissions.index', compact('data'));
    }

    public function create()
    {
        $this->authorize('create', Permission::class);

        return view('laravel-admin::admin.permissions.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:permissions',
            'description' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        $this->broadcastNotifier->resourceChanged(
            resource: $permission,
            resourceType: 'permission',
            event: 'created',
            title: '관리자 권한이 생성되었습니다',
        );

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully!');
    }

    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        return view('laravel-admin::admin.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);

        return view('laravel-admin::admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:permissions,name,'.$permission->id,
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        $this->broadcastNotifier->resourceChanged(
            resource: $permission,
            resourceType: 'permission',
            event: 'updated',
            title: '관리자 권한이 수정되었습니다',
            type: 'warning',
        );

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully!');
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $this->broadcastNotifier->resourceChanged(
            resource: $permission,
            resourceType: 'permission',
            event: 'deleted',
            title: '관리자 권한이 삭제되었습니다',
            type: 'warning',
            includeUrl: false,
        );

        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully!');
    }

    public function updateOrder(Request $request)
    {
        $this->authorize('update', Permission::class);

        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:permissions,id',
            'order.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->order as $item) {
                Permission::where('id', $item['id'])->update(['sort_order' => $item['order']]);
            }

            $permission = Permission::find(data_get($request->order, '0.id'));

            if ($permission) {
                $this->broadcastNotifier->resourceChanged(
                    resource: $permission,
                    resourceType: 'permission',
                    event: 'reordered',
                    title: '관리자 권한 순서가 변경되었습니다',
                    type: 'warning',
                    payload: ['updated_count' => count($request->order)],
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
            ], 500);
        }
    }
}
