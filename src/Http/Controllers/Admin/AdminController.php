<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class AdminController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewDashboard', 'admin');

        $adminUserModel = config('laravel-admin.admin_user_model', AdminUser::class);
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        $stats = [
            'total_admin_users' => $adminUserModel::query()->count(),
            'total_roles' => Role::query()->where('guard_name', $guard)->count(),
            'total_permissions' => Permission::query()->where('guard_name', $guard)->count(),
            'total_menu_categories' => MenuCategory::query()->count(),
        ];

        return view('laravel-admin::admin.index', compact('stats'));
    }
}
