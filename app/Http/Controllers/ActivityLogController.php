<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $logs = AuditLog::with('actor')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('activity.index', compact('logs'));
    }
}
