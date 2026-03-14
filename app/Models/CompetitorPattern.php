<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorPattern extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitor_id',
        'pattern_type',
        'pattern_data',
        'insights',
        'analysis_date',
    ];

    protected $casts = [
        'pattern_data' => 'array',
        'analysis_date' => 'date',
    ];

    /**
     * Get the competitor that owns the pattern
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get pattern type label
     */
    public function getPatternTypeLabelAttribute(): string
    {
        $labels = [
            'posting_time' => 'Waktu Posting',
            'frequency' => 'Frekuensi Posting',
            'tone' => 'Tone & Gaya Bahasa',
            'content_type' => 'Jenis Konten',
            'hashtag_usage' => 'Penggunaan Hashtag',
            'engagement_pattern' => 'Pola Engagement',
        ];

        return $labels[$this->pattern_type] ?? $this->pattern_type;
    }
}
