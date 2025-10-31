<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = UserActivityLog::with('user')
            ->orderBy('timestamp', 'desc')
            ->paginate(50);
        
        return view('activity.index', compact('logs'));
    }
}
