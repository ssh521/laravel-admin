<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allowedSortColumns = ['id', 'name', 'route_name', 'url', 'sort_order', 'is_active', 'category'];
        $sort = in_array($request->get('sort'), $allowedSortColumns, true) ? $request->get('sort') : 'id';
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $menus = Menu::with(['category', 'parent'])
            ->when(
                $request->search,
                function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%")
                        ->orWhere('route_name', 'like', "%{$request->search}%")
                        ->orWhere('url', 'like', "%{$request->search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($request) {
                            $categoryQuery->where('name', 'like', "%{$request->search}%");
                        });
                }
            )
            // 카테고리명 정렬 시 조인 사용
            ->when($sort === 'category', function ($query) use ($direction) {
                $query->leftJoin('menu_categories as mc', 'menus.category_id', '=', 'mc.id')
                    ->select('menus.*')
                    ->orderBy('mc.name', $direction);
            }, function ($query) use ($sort, $direction) {
                $query->orderBy($sort, $direction);
            })
            ->paginate(15);

        // 카테고리 목록도 함께 전달
        $categories = MenuCategory::active()->ordered()->get();

        return view('laravel-admin::admin.menus.index', compact('menus', 'categories', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = MenuCategory::active()->ordered()->get();
        $parentMenus = Menu::topLevel()->active()->ordered()->get();

        return view('laravel-admin::admin.menus.create', compact('categories', 'parentMenus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'route_name' => 'nullable|string|max:100',
            'route_parameters' => 'nullable|string|max:500',
            'url' => 'nullable|string|max:500',
            'external_link' => 'boolean',
            'target' => 'nullable|string|max:20',
            'category_id' => 'nullable|exists:menu_categories,id',
            'parent_id' => 'nullable|exists:menus,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Menu::create([
            'name' => $request->name,
            'route_name' => $request->route_name,
            'route_parameters' => $request->route_parameters,
            'url' => $request->url,
            'target' => $request->target ?? '_self',
            'category_id' => $request->category_id,
            'parent_id' => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.menus.index')
            ->with('success', '메뉴가 성공적으로 생성되었습니다.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $menu->load(['category', 'parent', 'children']);

        return view('laravel-admin::admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $categories = MenuCategory::active()->ordered()->get();
        $parentMenus = Menu::topLevel()->active()->ordered()->get();

        return view('laravel-admin::admin.menus.edit', compact('menu', 'categories', 'parentMenus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'route_name' => 'nullable|string|max:100',
            'route_parameters' => 'nullable|string|max:500',
            'url' => 'nullable|string|max:500',
            'external_link' => 'boolean',
            'target' => 'nullable|string|max:20',
            'category_id' => 'nullable|exists:menu_categories,id',
            'parent_id' => 'nullable|exists:menus,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // 자기 자신을 부모로 설정하는 것을 방지
        if ($request->parent_id == $menu->id) {
            return back()->withErrors(['parent_id' => '자기 자신을 부모로 설정할 수 없습니다.']);
        }

        $updateData = [
            'name' => $request->name,
            'route_name' => $request->route_name,
            'route_parameters' => $request->route_parameters,
            'url' => $request->url,
            'target' => $request->target ?? '_self',
            'category_id' => $request->category_id,
            'parent_id' => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
            'icon' => $request->icon,
            'description' => $request->description,
        ];

        // 디버깅을 위한 로그
        Log::info('Menu update data:', $updateData);
        Log::info('Request sort_order:', ['sort_order' => $request->sort_order]);

        $menu->update($updateData);

        return redirect()->route('admin.menus.index')
            ->with('success', '메뉴가 성공적으로 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // 하위 메뉴가 있는지 확인
        if ($menu->children()->count() > 0) {
            return back()->with('error', '하위 메뉴가 있어 삭제할 수 없습니다.');
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', '메뉴가 성공적으로 삭제되었습니다.');
    }

    /**
     * Toggle the active status of the menu
     */
    public function toggleStatus(Menu $menu)
    {
        $menu->update([
            'is_active' => ! $menu->is_active,
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $menu->is_active,
            'message' => $menu->is_active ? '활성화되었습니다.' : '비활성화되었습니다.',
        ]);
    }

    /**
     * Update the sort order of the menu
     */
    public function updateOrder(Request $request, Menu $menu)
    {
        $request->validate([
            'sort_order' => 'required|integer|min:0',
        ]);

        $menu->update([
            'sort_order' => $request->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => '정렬 순서가 업데이트되었습니다.',
        ]);
    }

    /**
     * 메뉴 검색 API
     *
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (empty($query)) {
            return response()->json([]);
        }

        $user = Auth::user();

        if (! $user) {
            return response()->json([]);
        }

        // 띄어쓰기 제거한 검색어 생성
        $normalizedQuery = preg_replace('/\s+/', '', $query);
        $superAdminRole = config('laravel-admin.super_admin_role', 'Super Admin');

        // 검색 결과를 우선순위에 따라 정렬하기 위해 raw SQL 사용
        $menus = Menu::with('category')
            ->where('is_active', true)
            ->whereHas('category', function ($categoryQuery) use ($user, $superAdminRole) {
                $categoryQuery->where('is_active', true);

                if (! $user->hasRole($superAdminRole)) {
                    $userRoleIds = $user->roles->pluck('id');

                    $categoryQuery->whereHas('roles', function ($roleQuery) use ($userRoleIds) {
                        $roleQuery->whereIn('role_id', $userRoleIds);
                    });
                }
            })
            ->where(function ($q) use ($query, $normalizedQuery) {
                // 원본 검색어로 검색
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                  // 띄어쓰기 제거한 검색어로도 검색
                    ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$normalizedQuery}%"])
                    ->orWhereRaw("REPLACE(description, ' ', '') LIKE ?", ["%{$normalizedQuery}%"])
                  // 카테고리명으로도 검색
                    ->orWhereHas('category', function ($categoryQuery) use ($query, $normalizedQuery) {
                        $categoryQuery->where('name', 'like', "%{$query}%")
                            ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$normalizedQuery}%"]);
                    });
            })
            ->orderByRaw('
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN description LIKE ? THEN 2
                    WHEN name LIKE ? THEN 3
                    WHEN description LIKE ? THEN 4
                    ELSE 5
                END
            ', ["%{$query}%", "%{$query}%", "%{$normalizedQuery}%", "%{$normalizedQuery}%"])
            ->orderBy('sort_order')
            ->limit(10)
            ->get()
            ->map(function ($menu) use ($query) {
                // 검색어가 어디서 매칭되었는지 확인
                $nameMatch = stripos($menu->name, $query) !== false;
                $descriptionMatch = $menu->description && stripos($menu->description, $query) !== false;

                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'description' => $menu->description,
                    'url' => $menu->url,
                    'target' => $menu->target ?? '_self',
                    'category' => $menu->category ? $menu->category->name : null,
                    'icon' => $menu->icon,
                    'nameMatch' => $nameMatch,
                    'descriptionMatch' => $descriptionMatch,
                ];
            });

        return response()->json($menus);
    }

    /**
     * 카테고리별 메뉴 목록 조회 (순서 변경용)
     *
     * @return JsonResponse
     */
    public function getMenusByCategory(MenuCategory $category)
    {
        $menus = Menu::where('category_id', $category->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'sort_order' => $menu->sort_order,
                    'icon' => $menu->icon,
                    'is_active' => $menu->is_active,
                ];
            });

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'menus' => $menus,
        ]);
    }

    /**
     * 메뉴 순서 일괄 업데이트
     *
     * @return JsonResponse
     */
    public function updateMenuOrder(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'menu_orders' => 'required|array',
            'menu_orders.*.id' => 'required|exists:menus,id',
            'menu_orders.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->menu_orders as $menuOrder) {
                Menu::where('id', $menuOrder['id'])
                    ->where('category_id', $request->category_id)
                    ->update(['sort_order' => $menuOrder['sort_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '메뉴 순서가 성공적으로 업데이트되었습니다.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu order update failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '메뉴 순서 업데이트에 실패했습니다.',
            ], 500);
        }
    }

    /**
     * 선택된 메뉴들의 카테고리 일괄 변경
     *
     * @return JsonResponse
     */
    public function updateCategoryMultiple(Request $request)
    {
        $request->validate([
            'menu_ids' => 'required|array|min:1',
            'menu_ids.*' => 'required|exists:menus,id',
            'category_id' => 'nullable|exists:menu_categories,id',
        ]);

        try {
            DB::beginTransaction();

            // 선택된 메뉴들의 카테고리 변경
            $updatedCount = Menu::whereIn('id', $request->menu_ids)
                ->update(['category_id' => $request->category_id]);

            DB::commit();

            $categoryName = $request->category_id
                ? MenuCategory::find($request->category_id)->name
                : '미분류';

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount}개 메뉴의 카테고리가 '{$categoryName}'로 변경되었습니다.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu category update failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '카테고리 변경에 실패했습니다.',
            ], 500);
        }
    }
}
