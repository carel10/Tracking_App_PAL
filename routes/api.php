<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

// Models
use App\Models\User;
use App\Models\AuditLog;
use App\Models\AuthSession;


/*
|--------------------------------------------------------------------------
| AUTH VERIFY (Dipanggil oleh Project PAL untuk Login)
|--------------------------------------------------------------------------
*/
Route::post('/auth-verify', function (Request $request) {

    $request->validate([
        'email'    => 'required|email',
        'password' => 'required'
    ]);

    // Cari user
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'valid'   => false,
            'message' => 'User tidak ditemukan.'
        ], 401);
    }

    // Password salah
    if (!Hash::check($request->password, $user->password_hash)) {

        AuditLog::create([
            'actor_user_id' => $user->id,
            'action'        => 'remote_login_failed',
            'target_table'  => 'users',
            'target_id'     => $user->id,
            'details'       => [
                'email'       => $request->email,
                'reason'      => 'wrong_password',
                'source_app'  => 'project_pal',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ],
        ]);

        return response()->json([
            'valid'   => false,
            'message' => 'Password salah.'
        ], 401);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN SUKSES — CATAT DI AUDIT LOG
    |--------------------------------------------------------------------------
    */
    AuditLog::create([
        'actor_user_id' => $user->id,
        'action'        => 'remote_login',
        'target_table'  => 'users',
        'target_id'     => $user->id,
        'details'       => [
            'via'        => 'external_project_pal',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 'Unknown',
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | CATAT SESSION BARU DI AUTH_SESSIONS
    | AGAR MUNCUL DI HALAMAN SESSION MONITORING
    |--------------------------------------------------------------------------
    */
    AuthSession::create([
        'user_id'     => $user->id,
        'issued_at'   => now(),
        'expires_at'  => now()->addDays(5),   // mengikuti pola session internal
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent() ?? 'RemoteLogin/project_pal',
    ]);


    // Ambil roles
    $roles = $user->roles()->pluck('name')->toArray();

    return response()->json([
        'valid' => true,
        'user'  => [
            'full_name'   => $user->full_name,
            'email'       => $user->email,
            'division_id' => $user->division_id,
            'roles'       => $roles,
        ]
    ]);
});


/*
|--------------------------------------------------------------------------
| REMOTE LOGOUT (Project PAL memanggil ini saat user logout)
|--------------------------------------------------------------------------
*/
Route::post('/remote-logout', function (Request $request) {

    if (!$request->user_id) {
        return response()->json(['message' => 'user_id missing'], 400);
    }

    AuditLog::create([
        'actor_user_id' => $request->user_id,
        'action'        => 'remote_logout',
        'target_table'  => 'users',
        'target_id'     => $request->user_id,
        'details'       => [
            'source_app' => 'project_pal',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ],
    ]);

    return response()->json(['message' => 'OK']);
});


/*
|--------------------------------------------------------------------------
| OPSIONAL — PENCATATAN LOGIN FAILED GENERIK
|--------------------------------------------------------------------------
*/
Route::post('/remote-login-failed', function (Request $request) {

    AuditLog::create([
        'actor_user_id' => null,
        'action'        => 'remote_login_failed',
        'target_table'  => 'users',
        'target_id'     => null,
        'details'       => [
            'email'       => $request->email,
            'reason'      => $request->reason ?? 'unknown',
            'source_app'  => 'project_pal',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ],
    ]);

    return response()->json(['message' => 'OK']);
});
