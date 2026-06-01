<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\MenuCategory;

class AdminController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewDashboard', 'admin');

        $userModel = config('laravel-admin.user_model', User::class);

        $stats = [
            'total_users' => $userModel::query()->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_menu_categories' => MenuCategory::query()->count(),
        ];

        return view('admin.index', compact('stats'));
    }
}
