<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'name',
        'content',
        'file_url',
        'file_original_extension',
        'has_been_analyzed',
        'file_hash',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(TextAnalysis::class);
    }

    public function analysisRequests(): HasMany
    {
        return $this->hasMany(AnalysisRequest::class);
    }

    public function corpus(): BelongsToMany
    {
        return $this->belongsToMany(Corpus::class, 'corpus_documents');
    }
}
