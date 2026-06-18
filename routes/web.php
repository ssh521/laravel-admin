<?php

use Illuminate\Support\Facades\Route;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\AdminUserController;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\AdminController;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\MenuCategoryController;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\MenuController;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\PermissionController;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\RoleController;
use Ssh521\LaravelAdmin\Http\Controllers\Auth\AdminLoginController;

if (config('laravel-admin.auth.admin_login.enabled', false)) {
    Route::prefix(config('laravel-admin.route_prefix', 'admin'))
        ->name(config('laravel-admin.route_name_prefix', 'admin.'))
        ->middleware(config('laravel-admin.auth.admin_login.middleware', ['web']))
        ->group(function () {
            Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
            Route::post('/login', [AdminLoginController::class, 'store'])->name('login.store');
            Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
        });
}

Route::get('adminuser/profile', [AdminUserController::class, 'profile'])
    ->name(config('laravel-admin.route_name_prefix', 'admin.').'profile.show')
    ->middleware(config('laravel-admin.middleware', ['web', 'auth', 'verified']));
Route::put('adminuser/profile', [AdminUserController::class, 'profileUpdate'])
    ->name(config('laravel-admin.route_name_prefix', 'admin.').'profile.update')
    ->middleware(config('laravel-admin.middleware', ['web', 'auth', 'verified']));

// Admin Routes
Route::prefix(config('laravel-admin.route_prefix', 'admin'))
    ->name(config('laravel-admin.route_name_prefix', 'admin.'))
    ->middleware(config('laravel-admin.middleware', ['web', 'auth', 'verified']))
    ->group(function () {
        // Route::view('링크', '파일위치')->name('이름');
        // 이름은 {{ route('admin.이름')}} 으로 사용해야함..

        Route::get('/', [AdminController::class, 'index'])
            ->middleware('can:admin-dashboard-access')
            ->name('index');

        // Admin User Routes
        Route::resource('admin-users', AdminUserController::class)
            ->middleware('can:super-admin-manage')
            ->parameters(['admin-users' => 'adminUser']);

        // Role Routes
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('can:role-list')->name('index');
            Route::get('/create', [RoleController::class, 'create'])->middleware('can:role-create')->name('create');
            Route::post('/', [RoleController::class, 'store'])->middleware('can:role-create')->name('store');
            Route::get('/{role}', [RoleController::class, 'show'])->middleware('can:role-view')->name('show');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('can:role-edit')->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->middleware('can:role-edit')->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('can:role-delete')->name('destroy');
        });

        // Permissions Routes
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->middleware('can:permission-list')->name('index');
            Route::get('/create', [PermissionController::class, 'create'])->middleware('can:permission-create')->name('create');
            Route::post('/', [PermissionController::class, 'store'])->middleware('can:permission-create')->name('store');
            Route::post('/update-order', [PermissionController::class, 'updateOrder'])->middleware('can:permission-edit')->name('update-order');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->middleware('can:permission-edit')->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('can:permission-edit')->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('can:permission-delete')->name('destroy');
        });

        // Menu Categories Routes
        Route::prefix('menu-categories')->name('menu-categories.')->group(function () {
            Route::get('/', [MenuCategoryController::class, 'index'])->middleware('can:menu-category-list')->name('index');
            Route::get('/create', [MenuCategoryController::class, 'create'])->middleware('can:menu-category-create')->name('create');
            Route::post('/', [MenuCategoryController::class, 'store'])->middleware('can:menu-category-create')->name('store');
            Route::get('/{menuCategory}/edit', [MenuCategoryController::class, 'edit'])->middleware('can:menu-category-edit')->name('edit');
            Route::put('/{menuCategory}', [MenuCategoryController::class, 'update'])->middleware('can:menu-category-edit')->name('update');
            Route::delete('/{menuCategory}', [MenuCategoryController::class, 'destroy'])->middleware('can:menu-category-delete')->name('destroy');
            Route::post('/{menuCategory}/toggle-status', [MenuCategoryController::class, 'toggleStatus'])->middleware('can:menu-category-edit')->name('toggle-status');
            Route::post('/{menuCategory}/update-order', [MenuCategoryController::class, 'updateOrder'])->middleware('can:menu-category-edit')->name('update-order');
            Route::post('/update-order-multiple', [MenuCategoryController::class, 'updateOrderMultiple'])->middleware('can:menu-category-edit')->name('update-order-multiple');
            // MenuCategory 권한(허용 Role) 관리
            Route::get('/{menuCategory}/roles', [MenuCategoryController::class, 'roles'])->middleware('can:menu-category-edit')->name('roles');
            Route::post('/{menuCategory}/roles', [MenuCategoryController::class, 'updateRoles'])->middleware('can:menu-category-edit')->name('roles.update');
            // MenuCategory API Routes (for AJAX)
            Route::get('/{menuCategory}/roles/api', [MenuCategoryController::class, 'getRoles'])->middleware('can:menu-category-view')->name('get-roles');
            Route::post('/{menuCategory}/roles/api', [MenuCategoryController::class, 'updateRolesApi'])->middleware('can:menu-category-edit')->name('update-roles-api');
            Route::get('/{menuCategory}/roles/modal', [MenuCategoryController::class, 'getRolesForModal'])->middleware('can:menu-category-view')->name('get-roles-modal');
            Route::get('/{menuCategory}', [MenuCategoryController::class, 'show'])->middleware('can:menu-category-view')->name('show');
        });

        // Menus Routes
        Route::prefix('menus')->name('menus.')->group(function () {
            Route::get('/', [MenuController::class, 'index'])->middleware('can:menu-list')->name('index');
            Route::get('/create', [MenuController::class, 'create'])->middleware('can:menu-create')->name('create');
            Route::post('/', [MenuController::class, 'store'])->middleware('can:menu-create')->name('store');
            Route::get('/search', [MenuController::class, 'search'])->middleware('can:menu-list')->name('search');
            Route::get('/category/{category}', [MenuController::class, 'getMenusByCategory'])->middleware('can:menu-view')->name('category');
            Route::post('/update-order-multiple', [MenuController::class, 'updateMenuOrder'])->middleware('can:menu-edit')->name('update-order-multiple');
            Route::post('/update-category-multiple', [MenuController::class, 'updateCategoryMultiple'])->middleware('can:menu-edit')->name('update-category-multiple');
            Route::get('/{menu}/edit', [MenuController::class, 'edit'])->middleware('can:menu-edit')->name('edit');
            Route::put('/{menu}', [MenuController::class, 'update'])->middleware('can:menu-edit')->name('update');
            Route::delete('/{menu}', [MenuController::class, 'destroy'])->middleware('can:menu-delete')->name('destroy');
            Route::post('/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->middleware('can:menu-edit')->name('toggle-status');
            Route::post('/{menu}/update-order', [MenuController::class, 'updateOrder'])->middleware('can:menu-edit')->name('update-order');
            Route::get('/{menu}', [MenuController::class, 'show'])->middleware('can:menu-view')->name('show');
        });
    });
