<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = [
        'name',
        'color',
        'status',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
