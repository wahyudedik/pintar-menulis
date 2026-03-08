<?php

namespace App\Services;

class OutputValidator
{
    /**
     * Validate AI output quality
     * 
     * @param string $output The generated caption/content
     * @param array $params Original generation parameters
     * @return array Validation result with score, errors, warnings
     */
    public function validate(string $output, array $params): array
    {
        $errors = [];
        $warnings = [];
        $score = 10.0;
        
        // 1. Check minimum length
        $wordCount = str_word_count($output);
        if ($wordCount < 15) {
            $errors[] = 'Output terlalu pendek (< 15 kata)';
            $score -= 3;
        } elseif ($wordCount < 25) {
            $warnings[] = 'Output agak pendek (< 25 kata)';
            $score -= 1;
        }
        
        // 2. Check hashtags if requested
        if ($params['auto_hashtag'] ?? false) {
            $hashtagCount = preg_match_all('/#[\w\u0080-\uFFFF]+/u', $output);
            if ($hashtagCount === 0) {
                $warnings[] = 'Hashtag tidak ditemukan';
                $score -= 1.5;
            } elseif ($hashtagCount < 3) {
                $warnings[] = 'Hashtag terlalu sedikit (< 3)';
                $score -= 0.5;
            }
        }
        
        // 3. Check CTA presence
        $ctaKeywords = ['order', 'beli', 'dm', 'klik', 'hubungi', 'pesan', 'daftar', 'checkout', 'wa', 'whatsapp', 'link bio', 'swipe up'];
        $hasCTA = false;
        foreach ($ctaKeywords as $keyword) {
            if (stripos($output, $keyword) !== false) {
                $hasCTA = true;
                break;
            }
        }
        if (!$hasCTA) {
            $warnings[] = 'CTA tidak jelas atau tidak ada';
            $score -= 1.5;
        }
        
        // 4. Check emoji presence (for casual/funny tone)
        $tone = $params['tone'] ?? 'casual';
        if (in_array($tone, ['casual', 'funny', 'emotional'])) {
            if (!preg_match('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $output)) {
                $warnings[] = 'Tidak ada emoji (recommended untuk tone ' . $tone . ')';
                $score -= 0.5;
            }
        }
        
        // 5. Check repetition with recent captions
        if (!empty($params['recent_captions'])) {
            foreach ($params['recent_captions'] as $recent) {
                $similarity = $this->calculateSimilarity($output, $recent);
                if ($similarity > 0.75) {
                    $errors[] = 'Terlalu mirip dengan caption sebelumnya (similarity: ' . round($similarity * 100) . '%)';
                    $score -= 5;
                    break;
                } elseif ($similarity > 0.6) {
                    $warnings[] = 'Agak mirip dengan caption sebelumnya (similarity: ' . round($similarity * 100) . '%)';
                    $score -= 2;
                    break;
                }
            }
        }
        
        // 6. Check platform-specific requirements
        $platform = $params['platform'] ?? 'instagram';
        $platformCheck = $this->validatePlatformRequirements($output, $platform);
        if (!$platformCheck['valid']) {
            $warnings = array_merge($warnings, $platformCheck['warnings']);
            $score -= $platformCheck['penalty'];
        }
        
        // 7. Check for spam-like patterns
        if ($this->hasSpamPatterns($output)) {
            $warnings[] = 'Terdeteksi pattern yang terlalu promotional/spam';
            $score -= 1;
        }
        
        // 8. Check language quality (basic)
        if ($this->hasLanguageIssues($output)) {
            $warnings[] = 'Terdeteksi potensi masalah bahasa';
            $score -= 0.5;
        }
        
        return [
            'valid' => empty($errors),
            'score' => max(0, min(10, $score)), // Clamp between 0-10
            'errors' => $errors,
            'warnings' => $warnings,
            'output' => $output,
            'word_count' => $wordCount
        ];
    }
    
    /**
     * Calculate similarity between two strings
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        // Normalize strings
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));
        
        // Remove hashtags and emojis for comparison
        $str1 = preg_replace('/#[\w\u0080-\uFFFF]+/u', '', $str1);
        $str2 = preg_replace('/#[\w\u0080-\uFFFF]+/u', '', $str2);
        $str1 = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $str1);
        $str2 = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $str2);
        
        // Calculate similarity
        similar_text($str1, $str2, $percent);
        return $percent / 100;
    }
    
    /**
     * Validate platform-specific requirements
     */
    protected function validatePlatformRequirements(string $output, string $platform): array
    {
        $warnings = [];
        $penalty = 0;
        
        $wordCount = str_word_count($output);
        
        switch ($platform) {
            case 'instagram':
                if ($wordCount > 200) {
                    $warnings[] = 'Caption terlalu panjang untuk Instagram (> 200 kata)';
                    $penalty += 1;
                }
                break;
                
            case 'tiktok':
                if ($wordCount > 120) {
                    $warnings[] = 'Caption terlalu panjang untuk TikTok (> 120 kata)';
                    $penalty += 1.5;
                }
                break;
                
            case 'twitter':
                $charCount = mb_strlen($output);
                if ($charCount > 280) {
                    $warnings[] = 'Caption terlalu panjang untuk Twitter (> 280 karakter)';
                    $penalty += 2;
                }
                break;
                
            case 'linkedin':
                if ($wordCount > 350) {
                    $warnings[] = 'Caption terlalu panjang untuk LinkedIn (> 350 kata)';
                    $penalty += 1;
                }
                break;
        }
        
        return [
            'valid' => empty($warnings),
            'warnings' => $warnings,
            'penalty' => $penalty
        ];
    }
    
    /**
     * Check for spam-like patterns
     */
    protected function hasSpamPatterns(string $output): bool
    {
        // Check for excessive capitalization
        $upperCount = preg_match_all('/[A-Z]/', $output);
        $totalLetters = preg_match_all('/[A-Za-z]/', $output);
        if ($totalLetters > 0 && ($upperCount / $totalLetters) > 0.4) {
            return true;
        }
        
        // Check for excessive exclamation marks
        $exclamationCount = substr_count($output, '!');
        if ($exclamationCount > 5) {
            return true;
        }
        
        // Check for excessive emoji
        $emojiCount = preg_match_all('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $output);
        if ($emojiCount > 15) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check for basic language issues
     */
    protected function hasLanguageIssues(string $output): bool
    {
        // Check for repeated words (more than 3 times in a row)
        if (preg_match('/(\b\w+\b)(\s+\1){3,}/i', $output)) {
            return true;
        }
        
        // Check for very long words (likely errors)
        if (preg_match('/\b\w{50,}\b/', $output)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if output should be retried
     */
    public function shouldRetry(array $validation, int $currentRetry = 0, int $maxRetries = 2): bool
    {
        // Don't retry if max retries reached
        if ($currentRetry >= $maxRetries) {
            return false;
        }
        
        // Retry if score too low or has critical errors
        return $validation['score'] < 6.0 || !$validation['valid'];
    }
}
