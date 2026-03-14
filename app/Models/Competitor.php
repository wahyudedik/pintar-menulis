<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'platform',
        'profile_name',
        'profile_picture',
        'bio',
        'followers_count',
        'following_count',
        'posts_count',
        'category',
        'is_active',
        'last_analyzed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_analyzed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the competitor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all posts for this competitor
     */
    public function posts(): HasMany
    {
        return $this->hasMany(CompetitorPost::class);
    }

    /**
     * Get patterns for this competitor
     */
    public function patterns(): HasMany
    {
        return $this->hasMany(CompetitorPattern::class);
    }

    /**
     * Get top content for this competitor
     */
    public function topContent(): HasMany
    {
        return $this->hasMany(CompetitorTopContent::class);
    }

    /**
     * Get content gaps for this competitor
     */
    public function contentGaps(): HasMany
    {
        return $this->hasMany(CompetitorContentGap::class);
    }

    /**
     * Get alerts for this competitor
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(CompetitorAlert::class);
    }

    /**
     * Get analysis summary for this competitor
     */
    public function analysisSummary(): HasMany
    {
        return $this->hasMany(CompetitorAnalysisSummary::class);
    }

    /**
     * Get latest analysis summary
     */
    public function latestSummary()
    {
        return $this->analysisSummary()->latest('analysis_date')->first();
    }

    /**
     * Get recent posts (last 30 days)
     */
    public function recentPosts()
    {
        return $this->posts()
            ->where('posted_at', '>=', now()->subDays(30))
            ->orderBy('posted_at', 'desc');
    }

    /**
     * Get top performing posts
     */
    public function topPerformingPosts($limit = 10)
    {
        return $this->posts()
            ->orderBy('engagement_rate', 'desc')
            ->limit($limit);
    }

    /**
     * Check if needs analysis (last analyzed > 24 hours ago)
     */
    public function needsAnalysis(): bool
    {
        if (!$this->last_analyzed_at) {
            return true;
        }
        
        return $this->last_analyzed_at->lt(now()->subHours(24));
    }

    /**
     * Get platform icon
     */
    public function getPlatformIconAttribute(): string
    {
        $icons = [
            // Social Media
            'instagram' => '📷',
            'tiktok' => '🎵',
            'facebook' => '👥',
            'youtube' => '📺',
            'twitter' => '🐦',
            'x' => '❌',
            'linkedin' => '💼',
            
            // Indonesian E-commerce
            'shopee' => '🛍️',
            'tokopedia' => '🟢',
            'lazada' => '🔵',
            'bukalapak' => '🔴',
            'blibli' => '🟠',
            'jdid' => '🔴',
            'zalora' => '👗',
            'sociolla' => '💄',
            'orami' => '👶',
            'bhinneka' => '💻',
            
            // International E-commerce
            'amazon' => '📦',
            'alibaba' => '🟡',
            'ebay' => '🔵',
            'etsy' => '🎨',
            'shopify' => '🛒',
        ];

        return $icons[$this->platform] ?? '🌐';
    }

    /**
     * Get platform color
     */
    public function getPlatformColorAttribute(): string
    {
        $colors = [
            // Social Media
            'instagram' => 'pink',
            'tiktok' => 'black',
            'facebook' => 'blue',
            'youtube' => 'red',
            'twitter' => 'sky',
            'x' => 'gray',
            'linkedin' => 'blue',
            
            // Indonesian E-commerce
            'shopee' => 'orange',
            'tokopedia' => 'green',
            'lazada' => 'blue',
            'bukalapak' => 'red',
            'blibli' => 'blue',
            'jdid' => 'red',
            'zalora' => 'purple',
            'sociolla' => 'pink',
            'orami' => 'orange',
            'bhinneka' => 'blue',
            
            // International E-commerce
            'amazon' => 'yellow',
            'alibaba' => 'orange',
            'ebay' => 'blue',
            'etsy' => 'orange',
            'shopify' => 'green',
        ];

        return $colors[$this->platform] ?? 'gray';
    }
}
