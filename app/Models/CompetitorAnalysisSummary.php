<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorAnalysisSummary extends Model
{
    use HasFactory;

    protected $table = 'competitor_analysis_summary';

    protected $fillable = [
        'competitor_id',
        'analysis_date',
        'total_posts',
        'avg_engagement_rate',
        'avg_likes',
        'avg_comments',
        'avg_shares',
        'top_hashtags',
        'posting_times',
        'dominant_tone',
        'content_types',
        'ai_insights',
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'avg_engagement_rate' => 'decimal:2',
        'top_hashtags' => 'array',
        'posting_times' => 'array',
        'content_types' => 'array',
    ];

    /**
     * Get the competitor that owns the summary
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get formatted average engagement rate
     */
    public function getFormattedAvgEngagementRateAttribute(): string
    {
        return number_format($this->avg_engagement_rate, 2) . '%';
    }

    /**
     * Get formatted average likes
     */
    public function getFormattedAvgLikesAttribute(): string
    {
        if ($this->avg_likes >= 1000000) {
            return number_format($this->avg_likes / 1000000, 1) . 'M';
        }

        if ($this->avg_likes >= 1000) {
            return number_format($this->avg_likes / 1000, 1) . 'K';
        }

        return number_format($this->avg_likes);
    }

    /**
     * Get posting frequency (calculated from total posts)
     */
    public function getPostingFrequencyAttribute(): float
    {
        // Assuming analysis covers 7 days, calculate posts per week
        return round($this->total_posts / 7 * 7, 1);
    }

    /**
     * Get best posting time
     */
    public function getBestPostingTimeAttribute(): string
    {
        if (!$this->posting_times || empty($this->posting_times)) {
            return 'N/A';
        }

        // Find time with highest engagement
        $bestTime = collect($this->posting_times)
            ->sortByDesc('avg_engagement')
            ->first();

        return $bestTime['time'] ?? 'N/A';
    }

    /**
     * Get most used content type
     */
    public function getMostUsedContentTypeAttribute(): ?string
    {
        if (!$this->content_types || empty($this->content_types)) {
            return null;
        }

        // Find content type with highest count
        $mostUsed = collect($this->content_types)
            ->sortByDesc('count')
            ->first();

        return $mostUsed['type'] ?? null;
    }

    /**
     * Get performance trend (compared to previous period)
     */
    public function getPerformanceTrend(): ?string
    {
        $previousSummary = static::where('competitor_id', $this->competitor_id)
            ->where('analysis_date', '<', $this->analysis_date)
            ->orderBy('analysis_date', 'desc')
            ->first();

        if (!$previousSummary) {
            return null;
        }

        $currentEngagement = $this->avg_engagement_rate;
        $previousEngagement = $previousSummary->avg_engagement_rate;

        if ($currentEngagement > $previousEngagement) {
            $increase = (($currentEngagement - $previousEngagement) / $previousEngagement) * 100;
            return 'up_' . round($increase, 1);
        } elseif ($currentEngagement < $previousEngagement) {
            $decrease = (($previousEngagement - $currentEngagement) / $previousEngagement) * 100;
            return 'down_' . round($decrease, 1);
        }

        return 'stable';
    }
}
