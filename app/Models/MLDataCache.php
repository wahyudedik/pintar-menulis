<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MLDataCache extends Model
{
    use HasFactory;

    protected $table = 'ml_data_cache';

    protected $fillable = [
        'username',
        'platform',
        'profile_data',
        'last_api_call',
        'api_calls_count',
        'api_calls_saved',
        'data_quality_score',
        'ml_insights',
        'cache_hit_count',
        'last_cache_hit'
    ];

    protected $casts = [
        'profile_data' => 'array',
        'ml_insights' => 'array',
        'last_api_call' => 'datetime',
        'last_cache_hit' => 'datetime',
        'data_quality_score' => 'float'
    ];

    /**
     * Increment cache hit counter
     */
    public function recordCacheHit(): void
    {
        $this->increment('cache_hit_count');
        $this->increment('api_calls_saved');
        $this->update(['last_cache_hit' => now()]);
    }

    /**
     * Check if data is fresh enough
     */
    public function isFresh(int $maxAgeHours = 24): bool
    {
        return $this->updated_at->diffInHours(now()) < $maxAgeHours;
    }

    /**
     * Get cache efficiency ratio
     */
    public function getCacheEfficiency(): float
    {
        if ($this->api_calls_count == 0) return 0;
        
        return round(($this->api_calls_saved / ($this->api_calls_count + $this->api_calls_saved)) * 100, 2);
    }

    /**
     * Scope for high quality data
     */
    public function scopeHighQuality($query)
    {
        return $query->where('data_quality_score', '>=', 70);
    }

    /**
     * Scope for recent data
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('updated_at', '>', now()->subHours($hours));
    }

    /**
     * Scope by platform
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}