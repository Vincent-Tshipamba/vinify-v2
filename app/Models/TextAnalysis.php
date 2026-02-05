<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'analysis_request_id',
        'document_id',
        'corpus_id',
        'similarities',
        'is_ai_generated',
        'plagiarism_percentage',
        'ai_generated_probability',
        'status',
        'error_message',
    ];

    protected $casts = [
        'similarities' => 'array',
        'is_ai_generated' => 'boolean',
        'plagiarism_percentage' => 'float',
        'ai_generated_probability' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analysisRequest(): BelongsTo
    {
        return $this->belongsTo(AnalysisRequest::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function corpus(): BelongsTo
    {
        return $this->belongsTo(Corpus::class);
    }
}
