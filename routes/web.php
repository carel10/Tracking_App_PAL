<?php

/**
 * Web Routes Configuration
 * 
 * File ini mendefinisikan semua routes web application.
 * Routes dikelompokkan menjadi:
 * 1. Public/Auth Routes - Route untuk login, logout, forgot password (tidak perlu auth)
 * 2. SSO Routes - Route untuk Single Sign-On (placeholder)
 * 3. Protected Routes - Route yang memerlukan authentication dan account status active
 * 
 * Middleware yang digunakan:
 * - auth: Memastikan user sudah login
 * - account.status: Memastikan user account status adalah 'active'
 * 
 * @package routes
 * @author Tracking App Team
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DelegatedAdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\SessionMonitoringController;
use App\Http\Controllers\SettingsController;

// ============================================
// PUBLIC / AUTH ROUTES (Tidak perlu authentication)
// ============================================

// Root route: redirect ke login page
Route::get('/', function () { return redirect()->route('login'); });

// Login routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login'); // Show login form
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // Process login
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Process logout

// Forgot password routes
Route::get('/password/forgot', [AuthController::class, 'showForgot'])->name('password.forgot'); // Show forgot password form
Route::post('/password/forgot', [AuthController::class, 'sendReset'])->name('password.send'); // Process password reset request

// ============================================
// SSO ROUTES (Single Sign-On - Placeholder)
// ============================================
Route::get('/sso/login', [AuthController::class, 'showSSOLogin'])->name('sso.login'); // Redirect to SSO provider
Route::get('/sso/callback', [AuthController::class, 'handleSSOCallback'])->name('sso.callback'); // Handle SSO callback

// ============================================
// PROTECTED ROUTES (Memerlukan authentication)
// ============================================
// Semua route di dalam group ini memerlukan:
// - User sudah login (middleware: auth)
// - User account status adalah 'active' (middleware: account.status)
Route::middleware(['auth', 'account.status'])->group(function () {
    // ========== DASHBOARD ==========
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ========== USER MANAGEMENT ==========
    // Resource routes untuk CRUD users (index, create, store, edit, update, destroy)
    // except(['show']) berarti route show tidak dibuat (tidak ada detail page)
    Route::resource('users', UserController::class)->except(['show']);
    
    // Custom routes untuk user operations
    Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle'); // Toggle active/inactive status
    Route::post('users/{user}/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign-roles'); // Assign roles to user
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password'); // Reset user password
    Route::get('users/{user}/sessions', [UserController::class, 'viewSessions'])->name('users.sessions'); // View user active sessions
    Route::post('users/{user}/force-logout', [UserController::class, 'forceLogout'])->name('users.force-logout'); // Force logout user

    // ========== ROLES & PERMISSIONS ==========
    // Resource routes untuk CRUD roles (full CRUD termasuk show)
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/users', [RoleController::class, 'viewUsers'])->name('roles.users'); // View all users with this role
    
    // Resource routes untuk CRUD permissions (full CRUD termasuk show)
    Route::resource('permissions', PermissionController::class);

    // ========== DIVISIONS MANAGEMENT ==========
    // Resource routes untuk CRUD divisions
    Route::resource('divisions', DivisionController::class);
    Route::get('divisions/{division}/users', [DivisionController::class, 'viewUsers'])->name('divisions.users'); // View all users in division
    Route::get('divisions/{division}/roles', [DivisionController::class, 'viewRoles'])->name('divisions.roles'); // View all roles in division

    // ========== DELEGATED ADMIN MANAGEMENT ==========
    // Routes untuk mengelola delegated admin (admin dengan scope terbatas per divisi)
    Route::get('delegated-admins', [DelegatedAdminController::class, 'index'])->name('delegated-admins.index'); // List all delegated admins
    Route::post('delegated-admins', [DelegatedAdminController::class, 'store'])->name('delegated-admins.store'); // Create new delegated admin
    Route::patch('delegated-admins/{adminScope}/permission', [DelegatedAdminController::class, 'updatePermission'])->name('delegated-admins.update-permission')->where('adminScope', '[0-9]+'); // Update permissions
    Route::delete('delegated-admins/{adminScope}', [DelegatedAdminController::class, 'destroy'])->name('delegated-admins.destroy')->where('adminScope', '[0-9]+'); // Remove delegated admin

    // ========== ACTIVITY LOGS ==========
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index'); // View activity logs
    
    // ========== AUDIT LOGS ==========
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index'); // View audit logs with filters
    
    // ========== SESSION MONITORING ==========
    Route::get('session-monitoring', [SessionMonitoringController::class, 'index'])->name('session-monitoring.index'); // View all active sessions
    Route::delete('session-monitoring/{sessionId}', [SessionMonitoringController::class, 'forceLogout'])->name('session-monitoring.force-logout')->where('sessionId', '[0-9]+'); // Force logout specific session
    Route::delete('session-monitoring/user/{userId}', [SessionMonitoringController::class, 'forceLogoutUser'])->name('session-monitoring.force-logout-user')->where('userId', '[0-9]+'); // Force logout all sessions for user
    Route::patch('session-monitoring/update-limit', [SessionMonitoringController::class, 'updateSessionLimit'])->name('session-monitoring.update-limit'); // Update session limit configuration
    
    // ========== SETTINGS ==========
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index'); // View settings page
    Route::patch('settings/authentication', [SettingsController::class, 'updateAuthentication'])->name('settings.update-authentication'); // Update authentication settings
    Route::patch('settings/password-policy', [SettingsController::class, 'updatePasswordPolicy'])->name('settings.update-password-policy'); // Update password policy
    Route::patch('settings/account-policy', [SettingsController::class, 'updateAccountPolicy'])->name('settings.update-account-policy'); // Update account policy
    Route::patch('settings/session-policy', [SettingsController::class, 'updateSessionPolicy'])->name('settings.update-session-policy'); // Update session policy
    Route::patch('settings/email', [SettingsController::class, 'updateEmailSettings'])->name('settings.update-email'); // Update email settings
    Route::post('settings/export', [SettingsController::class, 'export'])->name('settings.export'); // Export settings
});

// ============================================
// FALLBACK ROUTE (404 Not Found)
// ============================================
// Route ini akan menangani semua request yang tidak match dengan route di atas
// Menampilkan custom 404 error page
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
