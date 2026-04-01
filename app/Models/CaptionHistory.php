<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaptionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'caption_text',
        'category',
        'subcategory',
        'platform',
        'tone',
        'local_language',
        'brief_summary',
        'hash',
        'times_generated',
        'last_generated_at',
        'rating',
        'feedback',
        'is_public',
        'likes_count',
    ];

    protected $casts = [
        'last_generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate hash for caption text
     */
    public static function generateHash(string $text): string
    {
        // Normalize text: lowercase, remove extra spaces, remove punctuation
        $normalized = preg_replace('/[^\w\s]/u', '', strtolower($text));
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));
        
        return md5($normalized);
    }

    /**
     * Check if similar caption exists for user
     */
    public static function hasSimilarCaption(int $userId, string $text): bool
    {
        $hash = self::generateHash($text);
        
        return self::where('user_id', $userId)
            ->where('hash', $hash)
            ->exists();
    }

    /**
     * Get recent captions for user (for AI context)
     */
    public static function getRecentCaptions(int $userId, int $limit = 10, ?string $category = null, ?string $platform = null)
    {
        $query = self::where('user_id', $userId)
            ->orderBy('last_generated_at', 'desc')
            ->limit($limit);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($platform) {
            $query->where('platform', $platform);
        }
        
        return $query->get();
    }

    /**
     * Record new caption generation
     */
    public static function recordCaption(int $userId, string $caption, array $params = [])
    {
        $hash = self::generateHash($caption);
        
        // Check if similar caption exists
        $existing = self::where('user_id', $userId)
            ->where('hash', $hash)
            ->first();
        
        if ($existing) {
            // Update existing record
            $existing->increment('times_generated');
            $existing->update(['last_generated_at' => now()]);
            return $existing;
        }
        
        // Create new record
        return self::create([
            'user_id' => $userId,
            'caption_text' => $caption,
            'category' => $params['category'] ?? null,
            'subcategory' => $params['subcategory'] ?? null,
            'platform' => $params['platform'] ?? null,
            'tone' => $params['tone'] ?? null,
            'local_language' => $params['local_language'] ?? null,
            'brief_summary' => isset($params['brief']) ? substr($params['brief'], 0, 200) : null,
            'hash' => $hash,
            'last_generated_at' => now(),
        ]);
    }
}
