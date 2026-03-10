<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaptionKeyword extends Model
{
    protected $fillable = [
        'caption_history_id',
        'keyword',
        'search_volume',
        'competition',
        'cpc_low',
        'cpc_high',
        'relevance_score',
    ];

    protected $casts = [
        'search_volume' => 'integer',
        'cpc_low' => 'decimal:2',
        'cpc_high' => 'decimal:2',
        'relevance_score' => 'decimal:2',
    ];

    public function captionHistory(): BelongsTo
    {
        return $this->belongsTo(CaptionHistory::class);
    }

    /**
     * Get relevance percentage
     */
    public function getRelevancePercentageAttribute(): int
    {
        return $this->relevance_score ? (int)($this->relevance_score * 100) : 0;
    }
}
