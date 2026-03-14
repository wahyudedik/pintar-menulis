<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorTopContent extends Model
{
    use HasFactory;

    protected $table = 'competitor_top_content';

    protected $fillable = [
        'competitor_id',
        'competitor_post_id',
        'metric_type',
        'metric_value',
        'rank',
        'success_factors',
        'analysis_date',
    ];

    protected $casts = [
        'analysis_date' => 'date',
    ];

    /**
     * Get the competitor that owns the top content
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get the post
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(CompetitorPost::class, 'competitor_post_id');
    }

    /**
     * Get metric type label
     */
    public function getMetricTypeLabelAttribute(): string
    {
        $labels = [
            'engagement' => 'Engagement Rate',
            'likes' => 'Likes',
            'comments' => 'Comments',
            'shares' => 'Shares',
            'views' => 'Views',
        ];

        return $labels[$this->metric_type] ?? $this->metric_type;
    }

    /**
     * Get formatted metric value
     */
    public function getFormattedMetricValueAttribute(): string
    {
        if ($this->metric_type === 'engagement') {
            return number_format($this->metric_value, 2) . '%';
        }

        if ($this->metric_value >= 1000000) {
            return number_format($this->metric_value / 1000000, 1) . 'M';
        }

        if ($this->metric_value >= 1000) {
            return number_format($this->metric_value / 1000, 1) . 'K';
        }

        return number_format($this->metric_value);
    }
}
