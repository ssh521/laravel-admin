<?php

use Illuminate\Support\Facades\Route;
use Ssh521\LaravelAdmin\Http\Controllers\Admin\UserController;
use Ssh521\LaravelAdmin\Livewire\Admin\Users\UserTable;

$adminPrefix = trim(config('laravel-admin.route_prefix', 'admin'), '/');
$resourcePrefix = trim(config('laravel-admin.users.admin.route_prefix', 'users'), '/');
$namePrefix = config('laravel-admin.route_name_prefix', 'admin.').config('laravel-admin.users.admin.route_name_prefix', 'users.');
$middleware = config('laravel-admin.users.admin.middleware') ?: config('laravel-admin.middleware', ['web', 'auth', 'verified']);

Route::prefix($adminPrefix.'/'.$resourcePrefix)
    ->name($namePrefix)
    ->middleware($middleware)
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('can:user-list')->name('index');
        Route::get('/table', UserTable::class)->middleware('can:user-list')->name('table');
        Route::get('/create', [UserController::class, 'create'])->middleware('can:user-create')->name('create');
        Route::get('/select-user', [UserController::class, 'selectUser'])->middleware('can:user-list')->name('select-user');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('can:user-edit')->name('edit');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('can:user-view')->name('show');
        Route::post('/', [UserController::class, 'store'])->middleware('can:user-create')->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('can:user-edit')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('can:user-delete')->name('destroy');
    });
