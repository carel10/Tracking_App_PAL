<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;

// Public / auth routes
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/password/forgot', [AuthController::class, 'showForgot'])->name('password.forgot');
Route::post('/password/forgot', [AuthController::class, 'sendReset'])->name('password.send');

// Protected area (requires authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users management
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

    // Roles & Permissions
    Route::resource('roles', RoleController::class)->only(['index','create','store']);
    Route::resource('permissions', PermissionController::class)->only(['index','create','store']);

    // Activity logs
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
});

// Fallback route: use custom error page for 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
