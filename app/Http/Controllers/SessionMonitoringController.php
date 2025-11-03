<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthSession;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SessionMonitoringController extends Controller
{
    /**
     * Display a listing of all active sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AuthSession::with(['user.division'])
            ->where('expires_at', '>', now())
            ->orderBy('issued_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by IP address
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $sessions = $query->paginate(50);

        // Get statistics
        $totalActiveSessions = AuthSession::where('expires_at', '>', now())->count();
        $uniqueUsers = AuthSession::where('expires_at', '>', now())
            ->distinct('user_id')
            ->count('user_id');
        $uniqueIPs = AuthSession::where('expires_at', '>', now())
            ->whereNotNull('ip_address')
            ->distinct('ip_address')
            ->count('ip_address');

        // Get filter options
        $users = User::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get session limit config (from cache or default)
        $sessionLimit = Cache::get('session_limit_per_user', config('session.limit_per_user', 5));
        $sessionLifetime = Cache::get('session_lifetime_minutes', config('session.lifetime', 120)); // in minutes

        return view('session_monitoring.index', compact(
            'sessions',
            'totalActiveSessions',
            'uniqueUsers',
            'uniqueIPs',
            'users',
            'sessionLimit',
            'sessionLifetime'
        ));
    }

    /**
     * Force logout a specific session.
     *
     * @param  int  $sessionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceLogout($sessionId)
    {
        $session = AuthSession::findOrFail($sessionId);
        $user = $session->user;

        // Expire the session
        $session->expires_at = now();
        $session->save();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'session_force_logout',
            'target_table' => 'auth_sessions',
            'target_id' => $session->id,
            'details' => [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('session-monitoring.index')
            ->with('success', 'Session has been forcefully terminated.');
    }

    /**
     * Force logout all sessions for a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceLogoutUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Expire all active sessions for this user
        $sessionsCount = AuthSession::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_all_sessions_force_logout',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
                'sessions_terminated' => $sessionsCount,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('session-monitoring.index')
            ->with('success', "All sessions for {$user->full_name} have been forcefully terminated ({$sessionsCount} sessions).");
    }

    /**
     * Update session limit configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSessionLimit(Request $request)
    {
        $data = $request->validate([
            'session_limit' => 'required|integer|min:1|max:20',
            'session_lifetime' => 'required|integer|min:15|max:1440', // 15 minutes to 24 hours
        ]);

        // Store in cache or could be stored in database
        Cache::forever('session_limit_per_user', $data['session_limit']);
        Cache::forever('session_lifetime_minutes', $data['session_lifetime']);

        // Also update config if using config file approach
        // Note: In production, you might want to store this in database instead
        
        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'session_limit_config_updated',
            'target_table' => 'config',
            'target_id' => null,
            'details' => [
                'session_limit' => $data['session_limit'],
                'session_lifetime' => $data['session_lifetime'],
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('session-monitoring.index')
            ->with('success', 'Session limit configuration has been updated.');
    }

    /**
     * Check and enforce session limits for a user.
     *
     * @param  int  $userId
     * @return void
     */
    public static function enforceSessionLimit($userId)
    {
        $limit = Cache::get('session_limit_per_user', config('session.limit_per_user', 5));
        
        $activeSessions = AuthSession::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->orderBy('issued_at', 'asc')
            ->get();

        if ($activeSessions->count() > $limit) {
            // Expire oldest sessions
            $sessionsToExpire = $activeSessions->skip($limit);
            foreach ($sessionsToExpire as $session) {
                $session->expires_at = now();
                $session->save();
            }
        }
    }
}

