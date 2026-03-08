<?php

namespace App\Services;

class QualityScorer
{
    /**
     * Score caption quality on multiple dimensions
     * 
     * @param string $caption The generated caption
     * @param array $params Generation parameters
     * @return array Score breakdown and total
     */
    public function score(string $caption, array $params): array
    {
        $scores = [
            'hook_quality' => $this->scoreHook($caption),
            'engagement_potential' => $this->scoreEngagement($caption),
            'cta_effectiveness' => $this->scoreCTA($caption),
            'tone_match' => $this->scoreToneMatch($caption, $params['tone'] ?? 'casual'),
            'platform_optimization' => $this->scorePlatformOptimization($caption, $params['platform'] ?? 'instagram'),
            'readability' => $this->scoreReadability($caption),
            'uniqueness' => $this->scoreUniqueness($caption, $params['recent_captions'] ?? []),
        ];
        
        // Calculate weighted total (out of 10)
        $weights = [
            'hook_quality' => 0.20,
            'engagement_potential' => 0.20,
            'cta_effectiveness' => 0.15,
            'tone_match' => 0.15,
            'platform_optimization' => 0.10,
            'readability' => 0.10,
            'uniqueness' => 0.10,
        ];
        
        $totalScore = 0;
        foreach ($scores as $dimension => $score) {
            $totalScore += $score * $weights[$dimension];
        }
        
        return [
            'total_score' => round($totalScore, 2),
            'breakdown' => $scores,
            'grade' => $this->getGrade($totalScore),
            'recommendation' => $this->getRecommendation($totalScore, $scores)
        ];
    }
    
    /**
     * Score hook quality (first 1-2 sentences)
     */
    protected function scoreHook(string $caption): float
    {
        $score = 5.0; // Base score
        
        // Extract first sentence
        $sentences = preg_split('/[.!?]+/', $caption, 3);
        $hook = trim($sentences[0] ?? '');
        
        if (empty($hook)) {
            return 0;
        }
        
        // Check hook length (ideal: 5-15 words)
        $hookWords = str_word_count($hook);
        if ($hookWords >= 5 && $hookWords <= 15) {
            $score += 2;
        } elseif ($hookWords < 5) {
            $score += 1;
        }
        
        // Check for question (engaging)
        if (preg_match('/\?/', $hook)) {
            $score += 1.5;
        }
        
        // Check for emotional words
        $emotionalWords = ['wow', 'amazing', 'hebat', 'keren', 'gila', 'seru', 'asik', 'mantap', 'juara', 'top'];
        foreach ($emotionalWords as $word) {
            if (stripos($hook, $word) !== false) {
                $score += 0.5;
                break;
            }
        }
        
        // Check for curiosity gap
        $curiosityWords = ['rahasia', 'tips', 'cara', 'trik', 'hack', 'solusi', 'ternyata', 'fakta'];
        foreach ($curiosityWords as $word) {
            if (stripos($hook, $word) !== false) {
                $score += 1;
                break;
            }
        }
        
        return min(10, $score);
    }
    
    /**
     * Score engagement potential
     */
    protected function scoreEngagement(string $caption): float
    {
        $score = 5.0;
        
        // Check for questions (encourage comments)
        $questionCount = substr_count($caption, '?');
        $score += min(2, $questionCount * 0.5);
        
        // Check for engagement words
        $engagementWords = ['comment', 'komen', 'share', 'tag', 'mention', 'cerita', 'pengalaman', 'setuju', 'like'];
        foreach ($engagementWords as $word) {
            if (stripos($caption, $word) !== false) {
                $score += 0.5;
            }
        }
        
        // Check for emoji (visual engagement)
        $emojiCount = preg_match_all('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $caption);
        if ($emojiCount > 0 && $emojiCount <= 10) {
            $score += 1.5;
        } elseif ($emojiCount > 10) {
            $score += 0.5; // Too many emoji
        }
        
        // Check for storytelling elements
        $storyWords = ['dulu', 'sekarang', 'akhirnya', 'ternyata', 'awalnya', 'kemudian'];
        foreach ($storyWords as $word) {
            if (stripos($caption, $word) !== false) {
                $score += 0.5;
                break;
            }
        }
        
        return min(10, $score);
    }
    
    /**
     * Score CTA effectiveness
     */
    protected function scoreCTA(string $caption): float
    {
        $score = 0;
        
        // Check for CTA presence
        $ctaKeywords = [
            'strong' => ['order sekarang', 'beli sekarang', 'daftar sekarang', 'klik link', 'dm sekarang'],
            'medium' => ['order', 'beli', 'pesan', 'daftar', 'hubungi', 'dm', 'wa', 'klik'],
            'weak' => ['cek', 'lihat', 'intip']
        ];
        
        $hasCTA = false;
        foreach ($ctaKeywords['strong'] as $cta) {
            if (stripos($caption, $cta) !== false) {
                $score += 8;
                $hasCTA = true;
                break;
            }
        }
        
        if (!$hasCTA) {
            foreach ($ctaKeywords['medium'] as $cta) {
                if (stripos($caption, $cta) !== false) {
                    $score += 6;
                    $hasCTA = true;
                    break;
                }
            }
        }
        
        if (!$hasCTA) {
            foreach ($ctaKeywords['weak'] as $cta) {
                if (stripos($caption, $cta) !== false) {
                    $score += 3;
                    $hasCTA = true;
                    break;
                }
            }
        }
        
        // Bonus for urgency
        $urgencyWords = ['sekarang', 'hari ini', 'terbatas', 'segera', 'buruan', 'cepat', 'limited'];
        foreach ($urgencyWords as $word) {
            if (stripos($caption, $word) !== false) {
                $score += 1;
                break;
            }
        }
        
        // Bonus for specific contact method
        if (preg_match('/(wa|whatsapp|dm|link bio|shopee|tokopedia)/i', $caption)) {
            $score += 1;
        }
        
        return min(10, $score);
    }
    
    /**
     * Score tone match
     */
    protected function scoreToneMatch(string $caption, string $expectedTone): float
    {
        $score = 5.0; // Base score
        
        $caption_lower = strtolower($caption);
        
        switch ($expectedTone) {
            case 'casual':
                // Check for casual markers
                $casualWords = ['kak', 'bun', 'gaes', 'guys', 'nih', 'loh', 'dong', 'deh', 'sih'];
                foreach ($casualWords as $word) {
                    if (strpos($caption_lower, $word) !== false) {
                        $score += 1;
                        break;
                    }
                }
                // Check for emoji
                if (preg_match('/[\x{1F300}-\x{1F9FF}]/u', $caption)) {
                    $score += 1;
                }
                break;
                
            case 'formal':
                // Check for formal markers
                $formalWords = ['kami', 'anda', 'bapak', 'ibu', 'dengan hormat', 'terima kasih'];
                foreach ($formalWords as $word) {
                    if (strpos($caption_lower, $word) !== false) {
                        $score += 1;
                        break;
                    }
                }
                // Penalize excessive emoji
                $emojiCount = preg_match_all('/[\x{1F300}-\x{1F9FF}]/u', $caption);
                if ($emojiCount > 3) {
                    $score -= 2;
                }
                break;
                
            case 'funny':
                // Check for humor markers
                $funnyWords = ['wkwk', 'haha', 'hihi', 'lucu', 'ngakak', 'receh'];
                foreach ($funnyWords as $word) {
                    if (strpos($caption_lower, $word) !== false) {
                        $score += 1.5;
                        break;
                    }
                }
                break;
                
            case 'persuasive':
                // Check for persuasive markers
                $persuasiveWords = ['terbukti', 'dijamin', 'pasti', 'sudah', 'banyak yang', 'ratusan', 'ribuan'];
                foreach ($persuasiveWords as $word) {
                    if (strpos($caption_lower, $word) !== false) {
                        $score += 1;
                        break;
                    }
                }
                break;
                
            case 'emotional':
                // Check for emotional markers
                $emotionalWords = ['bahagia', 'sedih', 'terharu', 'bangga', 'senang', 'kecewa', 'menyentuh'];
                foreach ($emotionalWords as $word) {
                    if (strpos($caption_lower, $word) !== false) {
                        $score += 1.5;
                        break;
                    }
                }
                break;
        }
        
        return min(10, $score);
    }
    
    /**
     * Score platform optimization
     */
    protected function scorePlatformOptimization(string $caption, string $platform): float
    {
        $score = 5.0;
        $wordCount = str_word_count($caption);
        
        switch ($platform) {
            case 'instagram':
                // Ideal: 100-150 words
                if ($wordCount >= 100 && $wordCount <= 150) {
                    $score += 3;
                } elseif ($wordCount >= 80 && $wordCount <= 180) {
                    $score += 2;
                } else {
                    $score += 1;
                }
                // Check hashtags
                $hashtagCount = preg_match_all('/#[\w\x{0080}-\x{FFFF}]+/u', $caption);
                if ($hashtagCount >= 8 && $hashtagCount <= 15) {
                    $score += 2;
                } elseif ($hashtagCount >= 5) {
                    $score += 1;
                }
                break;
                
            case 'tiktok':
                // Ideal: 50-100 words (short)
                if ($wordCount >= 50 && $wordCount <= 100) {
                    $score += 4;
                } elseif ($wordCount <= 120) {
                    $score += 2;
                }
                // Check hashtags
                $hashtagCount = preg_match_all('/#[\w\x{0080}-\x{FFFF}]+/u', $caption);
                if ($hashtagCount >= 5 && $hashtagCount <= 10) {
                    $score += 1;
                }
                break;
                
            case 'facebook':
                // Ideal: 150-250 words (can be longer)
                if ($wordCount >= 150 && $wordCount <= 250) {
                    $score += 3;
                } elseif ($wordCount >= 100) {
                    $score += 2;
                }
                break;
                
            case 'linkedin':
                // Ideal: 200-300 words (professional)
                if ($wordCount >= 200 && $wordCount <= 300) {
                    $score += 4;
                } elseif ($wordCount >= 150) {
                    $score += 2;
                }
                break;
                
            case 'twitter':
                // Ideal: < 280 characters
                $charCount = mb_strlen($caption);
                if ($charCount <= 280) {
                    $score += 5;
                } else {
                    $score -= 3;
                }
                break;
        }
        
        return min(10, max(0, $score));
    }
    
    /**
     * Score readability
     */
    protected function scoreReadability(string $caption): float
    {
        $score = 5.0;
        
        // Check average sentence length
        $sentences = preg_split('/[.!?]+/', $caption);
        $sentences = array_filter($sentences, function($s) { return !empty(trim($s)); });
        $sentenceCount = count($sentences);
        
        if ($sentenceCount > 0) {
            $wordCount = str_word_count($caption);
            $avgWordsPerSentence = $wordCount / $sentenceCount;
            
            // Ideal: 10-20 words per sentence
            if ($avgWordsPerSentence >= 10 && $avgWordsPerSentence <= 20) {
                $score += 3;
            } elseif ($avgWordsPerSentence >= 8 && $avgWordsPerSentence <= 25) {
                $score += 2;
            } else {
                $score += 1;
            }
        }
        
        // Check paragraph breaks (line breaks)
        $lineBreaks = substr_count($caption, "\n");
        if ($lineBreaks >= 2) {
            $score += 2; // Good formatting
        }
        
        return min(10, $score);
    }
    
    /**
     * Score uniqueness (compared to recent captions)
     */
    protected function scoreUniqueness(string $caption, array $recentCaptions): float
    {
        if (empty($recentCaptions)) {
            return 10; // No comparison needed
        }
        
        $maxSimilarity = 0;
        foreach ($recentCaptions as $recent) {
            similar_text(strtolower($caption), strtolower($recent), $percent);
            $similarity = $percent / 100;
            $maxSimilarity = max($maxSimilarity, $similarity);
        }
        
        // Convert similarity to uniqueness score
        // 0% similarity = 10 score
        // 100% similarity = 0 score
        return round((1 - $maxSimilarity) * 10, 2);
    }
    
    /**
     * Get letter grade from score
     */
    protected function getGrade(float $score): string
    {
        if ($score >= 9) return 'A+';
        if ($score >= 8.5) return 'A';
        if ($score >= 8) return 'A-';
        if ($score >= 7.5) return 'B+';
        if ($score >= 7) return 'B';
        if ($score >= 6.5) return 'B-';
        if ($score >= 6) return 'C+';
        if ($score >= 5.5) return 'C';
        if ($score >= 5) return 'C-';
        return 'D';
    }
    
    /**
     * Get recommendation based on scores
     */
    protected function getRecommendation(float $totalScore, array $breakdown): string
    {
        if ($totalScore >= 8) {
            return 'Excellent! Caption berkualitas tinggi.';
        }
        
        // Find weakest dimension
        $weakest = array_keys($breakdown, min($breakdown))[0];
        
        $recommendations = [
            'hook_quality' => 'Perbaiki hook/opening untuk lebih menarik perhatian.',
            'engagement_potential' => 'Tambahkan elemen yang mendorong engagement (pertanyaan, ajakan diskusi).',
            'cta_effectiveness' => 'Perkuat call-to-action agar lebih jelas dan mendesak.',
            'tone_match' => 'Sesuaikan tone dengan target audience.',
            'platform_optimization' => 'Optimalkan panjang dan format untuk platform ini.',
            'readability' => 'Perbaiki struktur kalimat agar lebih mudah dibaca.',
            'uniqueness' => 'Buat caption yang lebih unik dan berbeda dari sebelumnya.',
        ];
        
        return $recommendations[$weakest] ?? 'Tingkatkan kualitas caption secara keseluruhan.';
    }
}
