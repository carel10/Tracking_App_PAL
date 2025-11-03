<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['actor.division']);

        // Filter by user (actor)
        if ($request->filled('user_id')) {
            $query->where('actor_user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by division
        if ($request->filled('division_id')) {
            $query->whereHas('actor', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by today, last week, last month
        if ($request->filled('time_period')) {
            switch ($request->time_period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter options
        $users = User::orderBy('full_name')->get();
        $divisions = Division::orderBy('name')->get();
        
        // Get distinct actions for filter dropdown
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Get statistics (from filtered query without pagination)
        $statsQuery = AuditLog::with(['actor']);
        
        // Apply same filters for statistics
        if ($request->filled('user_id')) {
            $statsQuery->where('actor_user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $statsQuery->where('action', $request->action);
        }
        if ($request->filled('division_id')) {
            $statsQuery->whereHas('actor', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }
        if ($request->filled('date_from')) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('time_period')) {
            switch ($request->time_period) {
                case 'today':
                    $statsQuery->whereDate('created_at', today());
                    break;
                case 'week':
                    $statsQuery->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $statsQuery->where('created_at', '>=', now()->subMonth());
                    break;
                case 'year':
                    $statsQuery->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        $totalLogs = $statsQuery->count();
        $todayLogs = (clone $statsQuery)->whereDate('created_at', today())->count();
        $uniqueUsers = (clone $statsQuery)->distinct('actor_user_id')->count('actor_user_id');
        $uniqueActions = (clone $statsQuery)->distinct('action')->count('action');

        return view('audit_logs.index', compact('logs', 'users', 'divisions', 'actions', 'totalLogs', 'todayLogs', 'uniqueUsers', 'uniqueActions'));
    }
}

