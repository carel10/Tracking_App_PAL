<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Division;
use App\Models\User;
use App\Models\Divisions;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $query = AuditLog::with(['actor']);

        if ($request->user_id) {
            $query->where('actor_user_id', $request->user_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->division_id) {
            $query->whereHas('actor', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // Time filters
        if ($request->time_period == 'today') {
            $query->whereDate('created_at', today());
        }
        elseif ($request->time_period == 'week') {
            $query->whereBetween('created_at', [now()->subWeek(), now()]);
        }
        elseif ($request->time_period == 'month') {
            $query->whereBetween('created_at', [now()->subMonth(), now()]);
        }
        elseif ($request->time_period == 'year') {
            $query->whereBetween('created_at', [now()->subYear(), now()]);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Dropdown filters
        $users = User::orderBy('full_name')->get();
        $actions = AuditLog::distinct()->pluck('action')->sort();
        $divisions = Division::orderBy('name')->get();

        // Stats
        $totalLogs = AuditLog::count();
        $todayLogs = AuditLog::whereDate('created_at', today())->count();
        $uniqueUsers = AuditLog::distinct('actor_user_id')->count('actor_user_id');
        $uniqueActions = AuditLog::distinct('action')->count('action');

        return view('activity.index', compact(
            'logs',
            'users',
            'actions',
            'divisions',
            'totalLogs',
            'todayLogs',
            'uniqueUsers',
            'uniqueActions'
        ));
    }
}
