<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
