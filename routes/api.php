<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ssh521\LaravelAdmin\Http\Controllers\Api\AuthController;
use Ssh521\LaravelAdmin\Http\Controllers\Api\UserController;

// Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    Route::get('users/{id}/roles', [UserController::class, 'getRoles'])->name('users.get-roles');
});
