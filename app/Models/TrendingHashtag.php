<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendingHashtag extends Model
{
    protected $fillable = [
        'hashtag',
        'platform',
        'trend_score',
        'usage_count',
        'engagement_rate',
        'category',
        'country',
        'last_updated',
    ];

    protected $casts = [
        'trend_score' => 'integer',
        'usage_count' => 'integer',
        'engagement_rate' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    /**
     * Get trending hashtags for platform
     */
    public static function getTrendingForPlatform(string $platform, int $limit = 20): array
    {
        return self::where('platform', $platform)
            ->where('country', 'ID')
            ->where('last_updated', '>=', now()->subDays(7))
            ->orderBy('trend_score', 'desc')
            ->limit($limit)
            ->pluck('hashtag')
            ->toArray();
    }

    /**
     * Get trending by category
     */
    public static function getTrendingByCategory(string $category, string $platform, int $limit = 10): array
    {
        return self::where('category', $category)
            ->where('platform', $platform)
            ->where('country', 'ID')
            ->where('last_updated', '>=', now()->subDays(7))
            ->orderBy('trend_score', 'desc')
            ->limit($limit)
            ->pluck('hashtag')
            ->toArray();
    }
}
