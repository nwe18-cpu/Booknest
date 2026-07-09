<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an admin/staff activity.
     *
     * @param string $action
     * @param string $description
     * @return void
     */
    public static function log($action, $description)
    {
        $staffId = Auth::guard('staff')->id();
        
        ActivityLog::create([
            'staff_id' => $staffId,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
}
