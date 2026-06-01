<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MenuCategory::class);

        $categories = MenuCategory::query()
            ->withCount('menus')
            ->with('roles')
            ->when(
                $request->search,
                function (Builder $builder) use ($request) {
                    $builder->where('name', 'like', "%{$request->search}%");
                }
            )
            ->ordered()
            ->paginate(perPage: 100);

        return view('admin.menu-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', MenuCategory::class);

        return view('admin.menu-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', MenuCategory::class);

        $request->validate([
            'name' => 'required|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $input = $request->all();
        $input['sort_order'] = $input['sort_order'] ?? 0;
        $input['is_active'] = $request->has('is_active');

        MenuCategory::create($input);

        return redirect()->route('admin.menu-categories.index')
            ->with('success', '메뉴 카테고리가 성공적으로 생성되었습니다.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuCategory $menuCategory)
    {
        $this->authorize('view', $menuCategory);

        $menuCategory->load(['menus' => function ($query) {
            $query->ordered();
        }, 'roles']);

        return view('admin.menu-categories.show', compact('menuCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        return view('admin.menu-categories.edit', compact('menuCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $request->validate([
            'name' => 'required|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $input = $request->all();
        $input['sort_order'] = $input['sort_order'] ?? 0;
        $input['is_active'] = $request->has('is_active');

        $menuCategory->update($input);

        return redirect()->route('admin.menu-categories.index')
            ->with('success', '메뉴 카테고리가 성공적으로 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuCategory $menuCategory)
    {
        $this->authorize('delete', $menuCategory);

        // 연결된 메뉴가 있는지 확인
        if ($menuCategory->menus()->count() > 0) {
            return back()->with('error', '이 카테고리에 속한 메뉴가 있어 삭제할 수 없습니다.');
        }

        $menuCategory->delete();

        return redirect()->route('admin.menu-categories.index')
            ->with('success', '메뉴 카테고리가 성공적으로 삭제되었습니다.');
    }

    /**
     * Toggle the active status of the category
     */
    public function toggleStatus(MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $menuCategory->update([
            'is_active' => ! $menuCategory->is_active,
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $menuCategory->is_active,
            'message' => $menuCategory->is_active ? '활성화되었습니다.' : '비활성화되었습니다.',
        ]);
    }

    /**
     * Update the sort order of the category
     */
    public function updateOrder(Request $request, MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $request->validate([
            'sort_order' => 'required|integer|min:0',
        ]);

        $menuCategory->update([
            'sort_order' => $request->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => '정렬 순서가 업데이트되었습니다.',
        ]);
    }

    /**
     * Update the sort order of multiple categories (for drag and drop)
     */
    public function updateOrderMultiple(Request $request)
    {
        $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*.id' => 'required|exists:menu_categories,id',
            'categories.*.sort_order' => 'required|integer|min:1',
        ]);

        $firstCategoryId = data_get($request->categories, '0.id');
        $firstCategory = MenuCategory::findOrFail($firstCategoryId);
        $this->authorize('update', $firstCategory);

        try {
            DB::beginTransaction();

            foreach ($request->categories as $categoryData) {
                $categoryId = data_get($categoryData, 'id');
                $sortOrder = data_get($categoryData, 'sort_order');

                MenuCategory::where('id', $categoryId)
                    ->update(['sort_order' => $sortOrder]);
            }

            DB::commit();

            // 로깅이 실패해도 API 응답 자체는 실패시키지 않기 위함
            try {
                Log::info('MenuCategoryController: 카테고리 순서 변경 완료');
            } catch (\Throwable $e) {
                // ignore
            }

            return response()->json([
                'success' => true,
                'message' => '카테고리 순서가 성공적으로 변경되었습니다.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            try {
                Log::error('MenuCategoryController: 카테고리 순서 변경 실패', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'categories' => $request->input('categories'),
                ]);
            } catch (\Throwable $logException) {
                // ignore
            }

            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.',
            ], 500);
        }
    }

    /**
     * 메뉴 카테고리별 허용 Role 관리 폼
     */
    public function roles(MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $roles = $this->getAvailableRoles();
        $allowedRoleIds = $menuCategory->roles->pluck('id')->toArray();

        return view('admin.menu-categories.roles', compact('menuCategory', 'roles', 'allowedRoleIds'));
    }

    /**
     * 메뉴 카테고리별 허용 Role 저장
     */
    public function updateRoles(Request $request, MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $this->syncMenuCategoryRoles($menuCategory, $request->roles);

        return redirect()->route('admin.menu-categories.index')
            ->with('success', '허용 역할이 저장되었습니다.');
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
     * Sync menu category roles
     */
    private function syncMenuCategoryRoles(MenuCategory $menuCategory, $roles)
    {
        if (isset($roles)) {
            $menuCategory->roles()->sync($roles);
        } else {
            $menuCategory->roles()->detach();
        }
    }

    /**
     * Get roles for a specific menu category (API)
     */
    public function getRoles(MenuCategory $menuCategory)
    {
        $this->authorize('view', $menuCategory);

        $roleIds = $menuCategory->roles->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'roles' => $roleIds,
        ]);
    }

    /**
     * Update roles for a specific menu category (API)
     */
    public function updateRolesApi(Request $request, MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $this->syncMenuCategoryRoles($menuCategory, $request->roles);

            return response()->json([
                'success' => true,
                'message' => '허용 역할이 저장되었습니다.',
            ]);
        } catch (\Exception $e) {
            Log::error('MenuCategoryController: 역할 저장 중 오류 발생', [
                'category_id' => $menuCategory->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '역할 저장 중 오류가 발생했습니다.',
            ], 500);
        }
    }

    /**
     * Get available roles and current roles for a specific menu category (API)
     */
    public function getRolesForModal(MenuCategory $menuCategory)
    {
        $this->authorize('view', $menuCategory);

        $availableRoles = $this->getAvailableRoles();
        $selectedRoles = $menuCategory->roles->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'availableRoles' => $availableRoles,
            'selectedRoles' => $selectedRoles,
            'categoryName' => $menuCategory->name,
        ]);
    }
}
