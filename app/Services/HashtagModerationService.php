<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Hashtag Moderation Service
 * 
 * Ensures hashtags are safe and appropriate before being used
 */
class HashtagModerationService
{
    /**
     * Blacklisted hashtags (inappropriate, spam, banned)
     */
    private array $blacklist = [
        // Spam/Scam
        '#scam', '#spam', '#fake', '#fraud',
        
        // Inappropriate
        '#porn', '#xxx', '#sex', '#adult',
        
        // Hate Speech
        '#hate', '#racist', '#discrimination',
        
        // Violence
        '#violence', '#kill', '#death',
        
        // Drugs
        '#drugs', '#marijuana', '#cocaine',
        
        // Gambling (if not allowed)
        '#gambling', '#casino', '#betting',
        
        // Add more as needed...
    ];
    
    /**
     * Warning keywords (need review but not auto-blocked)
     */
    private array $warningKeywords = [
        'judi', 'togel', 'slot', 'pinjol', 'pinjaman',
        'investasi', 'profit', 'cuan', 'passive income',
    ];
    
    /**
     * Check if hashtag is safe to use
     */
    public function isSafe(string $hashtag): bool
    {
        $hashtag = strtolower($hashtag);
        
        // Check blacklist
        if (in_array($hashtag, $this->blacklist)) {
            Log::warning('Blocked blacklisted hashtag', ['hashtag' => $hashtag]);
            return false;
        }
        
        // Check for suspicious patterns
        if ($this->hasSuspiciousPattern($hashtag)) {
            Log::warning('Blocked suspicious hashtag', ['hashtag' => $hashtag]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if hashtag needs manual review
     */
    public function needsReview(string $hashtag): bool
    {
        $hashtag = strtolower($hashtag);
        
        foreach ($this->warningKeywords as $keyword) {
            if (str_contains($hashtag, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Filter array of hashtags
     */
    public function filterHashtags(array $hashtags): array
    {
        $safe = [];
        $blocked = [];
        $needReview = [];
        
        foreach ($hashtags as $hashtag) {
            if (!$this->isSafe($hashtag)) {
                $blocked[] = $hashtag;
                continue;
            }
            
            if ($this->needsReview($hashtag)) {
                $needReview[] = $hashtag;
                // Still include but log for review
                Log::info('Hashtag needs review', ['hashtag' => $hashtag]);
            }
            
            $safe[] = $hashtag;
        }
        
        if (!empty($blocked)) {
            Log::warning('Blocked hashtags', [
                'count' => count($blocked),
                'hashtags' => $blocked
            ]);
        }
        
        return $safe;
    }
    
    /**
     * Check for suspicious patterns
     */
    private function hasSuspiciousPattern(string $hashtag): bool
    {
        // Too many numbers (likely spam)
        if (preg_match('/\d{4,}/', $hashtag)) {
            return true;
        }
        
        // Too many special characters
        if (preg_match('/[^a-z0-9#_]{3,}/', $hashtag)) {
            return true;
        }
        
        // Excessive repetition (e.g., #aaaaaaa)
        if (preg_match('/(.)\1{5,}/', $hashtag)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Validate hashtag quality
     */
    public function validateQuality(string $hashtag, array $metrics): bool
    {
        // Must have minimum engagement
        if (isset($metrics['engagement_rate']) && $metrics['engagement_rate'] < 1.0) {
            return false;
        }
        
        // Must have minimum trend score
        if (isset($metrics['trend_score']) && $metrics['trend_score'] < 50) {
            return false;
        }
        
        // Must have reasonable usage count
        if (isset($metrics['usage_count']) && $metrics['usage_count'] < 1000) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get blacklist (for admin management)
     */
    public function getBlacklist(): array
    {
        return $this->blacklist;
    }
    
    /**
     * Add to blacklist
     */
    public function addToBlacklist(string $hashtag): void
    {
        $hashtag = strtolower($hashtag);
        if (!in_array($hashtag, $this->blacklist)) {
            $this->blacklist[] = $hashtag;
            // TODO: Save to database for persistence
            Log::info('Added to blacklist', ['hashtag' => $hashtag]);
        }
    }
}
