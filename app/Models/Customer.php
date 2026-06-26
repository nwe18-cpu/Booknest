<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'address',
        'phone',
        'gender',
        'dob',
        'password',
        'image',
        'status',
        'subscription_status',
        'subscription_type',
        'subscription_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'password' => 'hashed',
        'subscription_expires_at' => 'datetime',
    ];

    public function hasActiveSubscription()
    {
        if ($this->subscription_status !== 'active') {
            return false;
        }

        if (is_null($this->subscription_expires_at)) {
            return false;
        }

        return $this->subscription_expires_at->isFuture();
    }

    public function getSubscriptionDaysLeft()
    {
        if (!$this->hasActiveSubscription()) {
            return 0;
        }

        $now = now();
        $diff = $now->diffInDays($this->subscription_expires_at, false);
        return $diff >= 0 ? (int)ceil($diff) : 0;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function readingProgresses()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function downloads()
    {
        return $this->hasMany(CustomerDownload::class);
    }

    public function downloadedBooks()
    {
        return $this->belongsToMany(Item::class, 'customer_downloads', 'customer_id', 'item_id')->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistBooks()
    {
        return $this->belongsToMany(Item::class, 'wishlists', 'customer_id', 'item_id')->withTimestamps();
    }
}
