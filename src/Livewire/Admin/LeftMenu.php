<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class LeftMenu extends Component
{
    public $sidebarMenuCategories;

    #[On('admin-menu-categories:menu-categories:reordered')]
    public function refreshMenuCategories()
    {
        // Log::info('LeftMenu: refreshMenuCategories 메서드 호출됨');
        $this->loadMenuCategories();
        // Log::info('LeftMenu: 메뉴 카테고리 새로고침 완료', [
        //     'count' => $this->sidebarMenuCategories->count()
        // ]);
    }

    #[On('admin-menus:menu-order:updated')]
    public function refreshMenuOrder()
    {
        // 메뉴 순서가 변경되었을 때 메뉴 카테고리 새로고침
        $this->loadMenuCategories();
    }

    public function mount()
    {
        $this->loadMenuCategories();
    }

    public function loadMenuCategories()
    {
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        Auth::shouldUse($guard);

        $user = Auth::guard($guard)->user();

        if (! $user) {
            $this->sidebarMenuCategories = collect();

            return;
        }

        // Super Admin은 모든 메뉴 카테고리 접근 가능
        if ($user->hasRole(config('laravel-admin.super_admin_role', 'Super Admin'))) {
            $this->sidebarMenuCategories = MenuCategory::with(['activeMenus' => function ($q) {
                $q->ordered();
            }])->active()->ordered()->get();
        } else {
            // 일반 사용자는 자신의 역할에 할당된 메뉴 카테고리만 접근 가능
            $userRoleIds = $user->roles->pluck('id');

            $this->sidebarMenuCategories = MenuCategory::with(['activeMenus' => function ($q) {
                $q->ordered();
            }])
                ->whereHas('roles', function ($query) use ($userRoleIds) {
                    $query->whereIn('role_id', $userRoleIds);
                })
                ->active()
                ->ordered()
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.admin.left-menu');
    }

    public function refresh()
    {
        $this->loadMenuCategories();
    }
}
