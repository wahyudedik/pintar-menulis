<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitor_id',
        'post_id',
        'caption',
        'post_type',
        'post_url',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
        'engagement_rate',
        'hashtags',
        'mentions',
        'posted_at',
    ];

    protected $casts = [
        'hashtags' => 'array',
        'mentions' => 'array',
        'posted_at' => 'datetime',
        'engagement_rate' => 'decimal:2',
    ];

    /**
     * Get the competitor that owns the post
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Calculate engagement rate
     */
    public function calculateEngagementRate(): float
    {
        $competitor = $this->competitor;
        
        if (!$competitor || $competitor->followers_count == 0) {
            return 0;
        }

        $totalEngagement = $this->likes_count + $this->comments_count + $this->shares_count;
        $engagementRate = ($totalEngagement / $competitor->followers_count) * 100;

        return round($engagementRate, 2);
    }

    /**
     * Get total engagement
     */
    public function getTotalEngagementAttribute(): int
    {
        return $this->likes_count + $this->comments_count + $this->shares_count;
    }

    /**
     * Check if post is viral (engagement rate > 10%)
     */
    public function isViral(): bool
    {
        return $this->engagement_rate > 10;
    }

    /**
     * Check if post is high performing (engagement rate > 5%)
     */
    public function isHighPerforming(): bool
    {
        return $this->engagement_rate > 5;
    }

    /**
     * Get post age in days
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->posted_at->diffInDays(now());
    }

    /**
     * Check if post contains promo/discount keywords
     */
    public function hasPromo(): bool
    {
        $promoKeywords = ['diskon', 'discount', 'promo', 'sale', 'murah', 'gratis', 'free', 'cashback', 'voucher'];
        $caption = strtolower($this->caption);
        
        foreach ($promoKeywords as $keyword) {
            if (str_contains($caption, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract hashtags from caption
     */
    public static function extractHashtags(string $caption): array
    {
        preg_match_all('/#(\w+)/', $caption, $matches);
        return $matches[1] ?? [];
    }

    /**
     * Extract mentions from caption
     */
    public static function extractMentions(string $caption): array
    {
        preg_match_all('/@(\w+)/', $caption, $matches);
        return $matches[1] ?? [];
    }
}
