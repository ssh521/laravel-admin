<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ssh521\LaravelAdmin\Http\Controllers\Api\AdminNoticeController;
use Ssh521\LaravelAdmin\Http\Controllers\Api\AuthController;
use Ssh521\LaravelAdmin\Http\Controllers\Api\UserController;

// Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Notice API Routes (인증 불필요)
Route::get('/public/notices', [AdminNoticeController::class, 'publicIndex'])->name('api.public.notices.index');
Route::get('/public/notices/{id}', [AdminNoticeController::class, 'publicShow'])->name('api.public.notices.show');

// User API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    Route::get('users/{id}/roles', [UserController::class, 'getRoles'])->name('users.get-roles');

    // Notice API Routes
    Route::apiResource('notices', AdminNoticeController::class)->names([
        'index' => 'api.notices.index',
        'store' => 'api.notices.store',
        'show' => 'api.notices.show',
        'update' => 'api.notices.update',
        'destroy' => 'api.notices.destroy',
    ]);
    Route::post('notices/{id}/toggle-publish', [AdminNoticeController::class, 'togglePublishStatus'])->name('api.notices.toggle-publish');
});
