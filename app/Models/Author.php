<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name',
        'image',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
