<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index()
    {
        $logs = ActivityLog::with(['staff.role'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.activity_logs.index', compact('logs'));
    }
}
