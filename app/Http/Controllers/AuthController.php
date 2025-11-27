<?php

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
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password_hash)) {

            if ($user->status !== 'active') {
                return back()->withErrors(['email' => 'Your account is not active.']);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            AuthSession::create([
                'user_id' => $user->id,
                'issued_at' => now(),
                'expires_at' => now()->addHours(config('session.lifetime', 2)),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            AuditLog::create([
                'actor_user_id' => $user->id,
                'action' => 'login',
                'target_table' => 'users',
                'target_id' => $user->id,
                'details' => [
                    'method' => 'password',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            return redirect()->intended(route('dashboard'));
        }

        AuditLog::create([
            'actor_user_id' => $user ? $user->id : null,
            'action' => 'login_failed',
            'target_table' => 'users',
            'target_id' => $user ? $user->id : null,
            'details' => [
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'actor_user_id' => Auth::id(),
                'action' => 'logout',
                'target_table' => 'users',
                'target_id' => Auth::id(),
                'details' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
