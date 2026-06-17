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

        Route::get('/', [AdminController::class, 'index'])->name('index');

        // Admin User Routes
        Route::resource('admin-users', AdminUserController::class)
            ->parameters(['admin-users' => 'adminUser']);

        // Role Routes
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        // Permissions Routes
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::get('/create', [PermissionController::class, 'create'])->name('create');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::post('/update-order', [PermissionController::class, 'updateOrder'])->name('update-order');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });

        // Menu Categories Routes
        Route::prefix('menu-categories')->name('menu-categories.')->group(function () {
            Route::get('/', [MenuCategoryController::class, 'index'])->name('index');
            Route::get('/create', [MenuCategoryController::class, 'create'])->name('create');
            Route::post('/', [MenuCategoryController::class, 'store'])->name('store');
            Route::get('/{menuCategory}/edit', [MenuCategoryController::class, 'edit'])->name('edit');
            Route::put('/{menuCategory}', [MenuCategoryController::class, 'update'])->name('update');
            Route::delete('/{menuCategory}', [MenuCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/{menuCategory}/toggle-status', [MenuCategoryController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{menuCategory}/update-order', [MenuCategoryController::class, 'updateOrder'])->name('update-order');
            Route::post('/update-order-multiple', [MenuCategoryController::class, 'updateOrderMultiple'])->name('update-order-multiple');
            // MenuCategory 권한(허용 Role) 관리
            Route::get('/{menuCategory}/roles', [MenuCategoryController::class, 'roles'])->name('roles');
            Route::post('/{menuCategory}/roles', [MenuCategoryController::class, 'updateRoles'])->name('roles.update');
            // MenuCategory API Routes (for AJAX)
            Route::get('/{menuCategory}/roles/api', [MenuCategoryController::class, 'getRoles'])->name('get-roles');
            Route::post('/{menuCategory}/roles/api', [MenuCategoryController::class, 'updateRolesApi'])->name('update-roles-api');
            Route::get('/{menuCategory}/roles/modal', [MenuCategoryController::class, 'getRolesForModal'])->name('get-roles-modal');
            Route::get('/{menuCategory}', [MenuCategoryController::class, 'show'])->name('show');
        });

        // Menus Routes
        Route::prefix('menus')->name('menus.')->group(function () {
            Route::get('/', [MenuController::class, 'index'])->name('index');
            Route::get('/create', [MenuController::class, 'create'])->name('create');
            Route::post('/', [MenuController::class, 'store'])->name('store');
            Route::get('/search', [MenuController::class, 'search'])->name('search');
            Route::get('/category/{category}', [MenuController::class, 'getMenusByCategory'])->name('category');
            Route::post('/update-order-multiple', [MenuController::class, 'updateMenuOrder'])->name('update-order-multiple');
            Route::post('/update-category-multiple', [MenuController::class, 'updateCategoryMultiple'])->name('update-category-multiple');
            Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
            Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
            Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
            Route::post('/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{menu}/update-order', [MenuController::class, 'updateOrder'])->name('update-order');
            Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
        });
    });
