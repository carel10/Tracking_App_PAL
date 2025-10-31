<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;

// Public / auth routes
Route::get('/', function () { return view('dashboard'); })->name('dashboard');
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard.index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/password/forgot', [AuthController::class, 'showForgot'])->name('password.forgot');
Route::post('/password/forgot', [AuthController::class, 'sendReset'])->name('password.send');

// Protected area (requires authentication)
Route::middleware(['web'])->group(function () {
    // Users management
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

    // Roles & Permissions
    Route::resource('roles', RoleController::class)->only(['index','create','store']);
    Route::resource('permissions', PermissionController::class)->only(['index','create','store']);

    // Activity logs
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
});
