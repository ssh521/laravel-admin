<?php

use Illuminate\Support\Facades\Route;
use Ssh521\LaravelAdmin\Http\Controllers\Api\UserController;

Route::get('/', [UserController::class, 'index'])->name('index');
Route::post('/', [UserController::class, 'store'])->name('store');
Route::get('/{user}', [UserController::class, 'show'])->name('show');
Route::put('/{user}', [UserController::class, 'update'])->name('update');
Route::patch('/{user}', [UserController::class, 'update'])->name('patch');
Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
