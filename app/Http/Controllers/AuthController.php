<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Update last login
            $user->update(['last_login' => now()]);
            
            // Log activity
            UserActivityLog::create([
                'user_id' => $user->user_id,
                'activity' => 'Logged in to the system',
                'timestamp' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Logged out from the system',
                'timestamp' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Default values for new registrations
        $defaultRoleId = 3; // User role
        $defaultDivisionId = 1; // IT Division

        $user = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'role_id' => $defaultRoleId,
            'division_id' => $defaultDivisionId,
            'status' => 'pending',
        ]);

        // Log registration
        UserActivityLog::create([
            'user_id' => $user->user_id,
            'activity' => 'Registered new account',
            'timestamp' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Log login activity as well
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Logged in to the system',
            'timestamp' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function sendReset(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $token = bin2hex(random_bytes(20));
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // For now, we won't send email automatically. Return token in session for developer use.
        return back()->with('status', 'Password reset token generated. (check logs or session)')->with('reset_token', $token);
    }
}
