<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistics
        $stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'inactiveUsers' => User::where('status', 'inactive')->count(),
            'todayLogins' => UserActivityLog::whereDate('timestamp', now()->toDateString())->count()
        ];

        // Top role by user count
        $topRole = Role::withCount('users')
            ->orderByDesc('users_count')
            ->first();

        // Latest registered user
        $lastUser = User::latest('created_at')->first();

        // Recent users with pagination
        $users = User::with(['role', 'division'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Recent activity logs
        $activities = UserActivityLog::with('user')
            ->orderBy('timestamp', 'desc')
            ->paginate(10);

        return view('dashboard', compact('stats', 'topRole', 'lastUser', 'users', 'activities'));
    }
}