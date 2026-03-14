<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorContentGap extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitor_id',
        'gap_type',
        'gap_title',
        'gap_description',
        'opportunity',
        'suggested_content',
        'priority',
        'is_implemented',
        'identified_date',
    ];

    protected $casts = [
        'suggested_content' => 'array',
        'is_implemented' => 'boolean',
        'identified_date' => 'date',
    ];

    /**
     * Get the competitor that owns the content gap
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get gap type label
     */
    public function getGapTypeLabelAttribute(): string
    {
        $labels = [
            'topic' => 'Topik Konten',
            'format' => 'Format Konten',
            'timing' => 'Waktu Posting',
            'tone' => 'Tone & Gaya',
            'hashtag' => 'Strategi Hashtag',
            'engagement' => 'Strategi Engagement',
        ];

        return $labels[$this->gap_type] ?? $this->gap_type;
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        if ($this->priority >= 8) {
            return 'Sangat Tinggi';
        } elseif ($this->priority >= 6) {
            return 'Tinggi';
        } elseif ($this->priority >= 4) {
            return 'Sedang';
        } else {
            return 'Rendah';
        }
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        if ($this->priority >= 8) {
            return 'red';
        } elseif ($this->priority >= 6) {
            return 'orange';
        } elseif ($this->priority >= 4) {
            return 'yellow';
        } else {
            return 'gray';
        }
    }

    /**
     * Mark as implemented
     */
    public function markAsImplemented(): void
    {
        $this->update(['is_implemented' => true]);
    }
}
