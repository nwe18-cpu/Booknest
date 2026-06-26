<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'order_id',
        'receiver_name',
        'phone_number',
        'address_line',
        'email',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
