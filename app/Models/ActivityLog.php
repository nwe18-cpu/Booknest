<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'staff_id',
        'action',
        'description',
        'ip_address',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
