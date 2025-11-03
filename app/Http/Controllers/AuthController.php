<?php

/**
 * AuthController
 * 
 * Controller ini menangani semua operasi autentikasi dan otorisasi user:
 * - Login (dengan validasi dan audit logging)
 * - Logout (dengan audit logging)
 * - Forgot Password (reset password)
 * - SSO Authentication (placeholder untuk future implementation)
 * 
 * Setiap operasi login/logout dicatat di audit log untuk keamanan.
 * Sistem mendukung multiple active sessions per user.
 * 
 * @package App\Http\Controllers
 * @author Tracking App Team
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\AuthSession;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     * 
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses request login user
     * 
     * Method ini melakukan:
     * 1. Validasi email dan password
     * 2. Mencari user berdasarkan email
     * 3. Verifikasi password menggunakan Hash::check
     * 4. Mengecek status user (harus active)
     * 5. Membuat session Laravel dan AuthSession
     * 6. Mencatat audit log untuk login berhasil
     * 7. Redirect ke dashboard
     * 
     * Jika login gagal, dicatat di audit log sebagai login_failed.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input: email harus valid email, password wajib diisi
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // Mencari user berdasarkan email
        $user = User::where('email', $credentials['email'])->first();
        
        // Verifikasi password dan cek apakah user ditemukan
        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            // Cek apakah user status active (bukan inactive atau suspended)
            if ($user->status !== 'active') {
                return back()->withErrors(['email' => 'Your account is not active. Please contact administrator.']);
            }

            // Login user ke Laravel Auth system
            // $request->boolean('remember') mengaktifkan "remember me" jika checkbox dicentang
            Auth::login($user, $request->boolean('remember'));
            
            // Regenerate session ID untuk security (prevent session fixation attack)
            $request->session()->regenerate();
            
            // Membuat record AuthSession untuk tracking session aktif
            // Session expiry dihitung berdasarkan config session.lifetime (default 2 jam)
            $sessionExpiry = now()->addHours(config('session.lifetime', 2));
            AuthSession::create([
                'user_id' => $user->id,
                'issued_at' => now(),
                'expires_at' => $sessionExpiry,
                'ip_address' => $request->ip(), // IP address user saat login
                'user_agent' => $request->userAgent(), // Browser/client yang digunakan
            ]);

            // Mencatat audit log untuk login berhasil
            AuditLog::create([
                'actor_user_id' => $user->id,
                'action' => 'login',
                'target_table' => 'users',
                'target_id' => $user->id,
                'details' => [
                    'method' => 'password', // Login method: password atau SSO
                    'ip_address' => $request->ip(),
                ],
                'created_at' => now(),
            ]);

            // Redirect ke dashboard (atau URL yang di-intended sebelumnya)
            return redirect()->intended(route('dashboard'));
        }

        // Log failed login attempt untuk security tracking
        // actor_user_id bisa null jika email tidak ditemukan, atau user ID jika password salah
        AuditLog::create([
            'actor_user_id' => $user ? $user->id : null,
            'action' => 'login_failed',
            'target_table' => 'users',
            'target_id' => $user ? $user->id : null,
            'details' => [
                'email' => $credentials['email'], // Email yang dicoba (untuk tracking brute force)
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'created_at' => now(),
        ]);

        // Return error message (tidak reveal apakah email atau password yang salah - security)
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    /**
     * Memproses logout user
     * 
     * Method ini melakukan:
     * 1. Mencatat audit log untuk logout (jika user masih authenticated)
     * 2. Logout user dari Laravel Auth
     * 3. Invalidate session
     * 4. Regenerate CSRF token
     * 5. Redirect ke halaman login
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Mencatat audit log sebelum logout (jika user masih authenticated)
        if (Auth::check()) {
            AuditLog::create([
                'actor_user_id' => Auth::id(),
                'action' => 'logout',
                'target_table' => 'users',
                'target_id' => Auth::id(),
                'details' => [
                    'ip_address' => $request->ip(),
                ],
                'created_at' => now(),
            ]);
        }

        // Logout user dari Laravel Auth system
        Auth::logout();
        
        // Invalidate session (menghapus session data)
        $request->session()->invalidate();
        
        // Regenerate CSRF token untuk security
        $request->session()->regenerateToken();
        
        // Redirect ke halaman login
        return redirect()->route('login');
    }

    /**
     * Menampilkan halaman forgot password
     * 
     * @return \Illuminate\View\View
     */
    public function showForgot()
    {
        return view('auth.forgot');
    }

    /**
     * Memproses request reset password
     * 
     * Method ini:
     * 1. Validasi email
     * 2. Generate random token untuk reset password
     * 3. Menyimpan token (hashed) ke database
     * 
     * NOTE: Untuk saat ini, email tidak dikirim otomatis.
     * Token dikembalikan dalam session untuk developer use.
     * Di production, implementasikan email sending.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendReset(Request $request)
    {
        // Validasi email
        $data = $request->validate(['email' => 'required|email']);
        
        // Generate random token (40 karakter hex)
        $token = bin2hex(random_bytes(20));
        
        // Simpan atau update token di database (token di-hash untuk security)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Untuk saat ini, token dikembalikan dalam session (untuk developer/testing)
        // TODO: Implement email sending di production
        return back()->with('status', 'Password reset token generated. (check logs or session)')->with('reset_token', $token);
    }

    /**
     * Menampilkan halaman SSO login atau redirect ke SSO provider
     * 
     * NOTE: Method ini adalah placeholder untuk future SSO integration.
     * SSO (Single Sign-On) memungkinkan user login menggunakan external provider
     * seperti Google, Microsoft, dll.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showSSOLogin()
    {
        // TODO: Implement SSO login logic
        // This is a placeholder for SSO integration
        // When SSO is configured, this should redirect to SSO provider
        return redirect()->route('login')->withErrors(['email' => 'SSO authentication is not yet configured.']);
    }

    /**
     * Menangani callback dari SSO provider
     * 
     * Method ini akan dipanggil setelah SSO provider mengautentikasi user
     * dan mengirim response kembali ke aplikasi.
     * 
     * NOTE: Method ini adalah placeholder untuk future SSO integration.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleSSOCallback(Request $request)
    {
        // TODO: Implement SSO callback logic
        // This will handle the response from SSO provider
        // For now, return to login with error
        return redirect()->route('login')->withErrors(['email' => 'SSO authentication is not yet configured.']);
    }
}
