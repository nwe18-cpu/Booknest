<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'author_id',
        'name',
        'pages',
        'pages_content',
        'price',
        'stock_quantity',
        'description',
        'image',
        'pdf_file',
        'status',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function readingProgresses()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function classifications()
    {
        return $this->belongsToMany(Classification::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(Customer::class, 'wishlists', 'item_id', 'customer_id')->withTimestamps();
    }
}
