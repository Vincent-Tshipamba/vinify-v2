<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Corpus extends Model
{
    protected $table = 'corpus';
    protected $fillable = [
        'type',
        'university_id',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'corpus_documents');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(TextAnalysis::class);
    }
}
