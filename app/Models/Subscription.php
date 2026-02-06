<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;
    protected $fillable = ['name', 'max_users', 'max_analyses', 'price'];
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function hasActiveSubscription()
    {
        return $this->university && $this->university->subscription;
    }

}