<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDownload extends Model
{
    protected $fillable = [
        'customer_id',
        'item_id',
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
