<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaptionAnalytics extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'caption_text',
        'category',
        'subcategory',
        'platform',
        'tone',
        'likes',
        'comments',
        'shares',
        'saves',
        'reach',
        'impressions',
        'clicks',
        'engagement_rate',
        'user_rating',
        'user_notes',
        'marked_as_successful',
        'used_for_training',
        'posted_at',
        'metrics_updated_at',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'metrics_updated_at' => 'datetime',
        'marked_as_successful' => 'boolean',
        'used_for_training' => 'boolean',
        'engagement_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Calculate engagement rate based on metrics
     */
    public function calculateEngagementRate(): float
    {
        if ($this->reach == 0 && $this->impressions == 0) {
            return 0;
        }

        $base = $this->reach > 0 ? $this->reach : $this->impressions;
        $engagements = $this->likes + $this->comments + $this->shares + $this->saves;
        
        return round(($engagements / $base) * 100, 2);
    }

    /**
     * Update engagement rate
     */
    public function updateEngagementRate(): void
    {
        $this->engagement_rate = $this->calculateEngagementRate();
        $this->metrics_updated_at = now();
        $this->save();
    }

    /**
     * Scope for successful captions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('marked_as_successful', true)
            ->orWhere('engagement_rate', '>=', 5); // 5% or higher
    }

    /**
     * Scope for training data
     */
    public function scopeForTraining($query)
    {
        return $query->where('used_for_training', true);
    }

    /**
     * Scope by platform
     */
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
