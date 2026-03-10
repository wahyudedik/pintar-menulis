<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HashtagBlacklist extends Model
{
    protected $table = 'hashtag_blacklist';
    
    protected $fillable = [
        'hashtag',
        'reason',
        'notes',
        'added_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get user who added this to blacklist
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Check if hashtag is blacklisted
     */
    public static function isBlacklisted(string $hashtag): bool
    {
        return self::where('hashtag', strtolower($hashtag))
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all active blacklisted hashtags
     */
    public static function getActiveBlacklist(): array
    {
        return self::where('is_active', true)
            ->pluck('hashtag')
            ->toArray();
    }
}
