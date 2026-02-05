<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class University extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'description',
        'slug',
        'admin_id',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function analysisRequests(): HasMany
    {
        return $this->hasMany(AnalysisRequest::class);
    }

    public function corpus(): HasOne
    {
        return $this->hasOne(Corpus::class);
    }
}
