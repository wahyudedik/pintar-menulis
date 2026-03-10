<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeywordResearch extends Model
{
    protected $table = 'keyword_research';
    
    protected $fillable = [
        'user_id',
        'keyword',
        'search_volume',
        'competition',
        'cpc_low',
        'cpc_high',
        'trend_direction',
        'trend_percentage',
        'related_keywords',
        'last_updated',
    ];

    protected $casts = [
        'search_volume' => 'integer',
        'cpc_low' => 'decimal:2',
        'cpc_high' => 'decimal:2',
        'trend_percentage' => 'integer',
        'related_keywords' => 'array',
        'last_updated' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get competition level display
     */
    public function getCompetitionLevelAttribute(): string
    {
        return match($this->competition) {
            'LOW' => 'Rendah',
            'MEDIUM' => 'Sedang',
            'HIGH' => 'Tinggi',
            default => 'Unknown',
        };
    }

    /**
     * Get trend emoji
     */
    public function getTrendEmojiAttribute(): string
    {
        return match($this->trend_direction) {
            'UP' => '↗️',
            'DOWN' => '↘️',
            'STABLE' => '→',
            default => '→',
        };
    }

    /**
     * Check if data is fresh (less than 7 days old)
     */
    public function isFresh(): bool
    {
        return $this->last_updated && $this->last_updated->gt(now()->subDays(7));
    }

    /**
     * Get average CPC
     */
    public function getAverageCpcAttribute(): ?float
    {
        if ($this->cpc_low && $this->cpc_high) {
            return ($this->cpc_low + $this->cpc_high) / 2;
        }
        return null;
    }
}
