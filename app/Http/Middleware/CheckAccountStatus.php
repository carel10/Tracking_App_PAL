<?php

/**
 * CheckAccountStatus Middleware
 * 
 * Middleware ini memastikan bahwa hanya user dengan status 'active' 
 * yang dapat mengakses route yang dilindungi.
 * 
 * Jika user yang sudah login memiliki status selain 'active' 
 * (inactive, suspended, pending), maka:
 * 1. User akan di-logout secara otomatis
 * 2. Session di-invalidate
 * 3. CSRF token di-regenerate
 * 4. Menampilkan error page dengan status 403 (Forbidden)
 * 
 * Middleware ini diterapkan pada semua protected routes untuk
 * mencegah user yang tidak aktif mengakses sistem.
 * 
 * @package App\Http\Middleware
 * @author Tracking App Team
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle incoming request untuk mengecek status account user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next Closure untuk melanjutkan request ke handler berikutnya
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cek apakah account user tidak aktif (inactive, suspended, pending)
            if ($user->status !== 'active') {
                // Logout user secara otomatis
                Auth::logout();
                
                // Invalidate session untuk menghapus semua data session
                $request->session()->invalidate();
                
                // Regenerate CSRF token untuk security
                $request->session()->regenerateToken();
                
                // Return error page dengan status code 403 (Forbidden)
                return response()->view('errors.account-suspended', [], 403);
            }
        }

        // Jika user active atau belum login, lanjutkan request ke handler berikutnya
        return $next($request);
    }
}

