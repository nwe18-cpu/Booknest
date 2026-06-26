<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    protected $table = 'reading_progress';

    protected $fillable = [
        'customer_id',
        'item_id',
        'current_page',
        'bookmarked_page',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'current_page' => 'integer',
        'bookmarked_page' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
