<?php

namespace App\Services;

use App\Models\MLOptimizedData;
use App\Models\CaptionAnalytics;
use App\Models\MLTrainingLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * ML Training Service
 * 
 * Auto-trains ML model from real analytics data
 * Runs daily to improve performance
 */
class MLTrainingService
{
    /**
     * Run daily training from analytics data
     */
    public function runDailyTraining(): array
    {
        $startTime = now();
        $results = [
            'hashtags' => 0,
            'keywords' => 0,
            'topics' => 0,
            'hooks' => 0,
            'ctas' => 0,
            'errors' => [],
        ];
        
        try {
            DB::beginTransaction();
            
            // Train hashtags
            $results['hashtags'] = $this->trainHashtags();
            
            // Train keywords
            $results['keywords'] = $this->trainKeywords();
            
            // Train topics
            $results['topics'] = $this->trainTopics();
            
            // Train hooks
            $results['hooks'] = $this->trainHooks();
            
            // Train CTAs
            $results['ctas'] = $this->trainCTAs();
            
            // Clean old low-performing data
            $this->cleanLowPerformingData();
            
            DB::commit();
            
            // Log training
            $this->logTraining($results, $startTime);
            
            Log::info('ML Training completed successfully', $results);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
            Log::error('ML Training failed: ' . $e->getMessage());
        }
        
        return $results;
    }
    
    /**
     * Train hashtags from high-performing captions
     */
    private function trainHashtags(): int
    {
        $count = 0;
        
        // Get captions with high engagement (> 5%)
        $highPerformingCaptions = CaptionAnalytics::whereNotNull('engagement_rate')
            ->where('engagement_rate', '>', 5.0)
            ->where('created_at', '>', now()->subDays(30))
            ->get();
        
        foreach ($highPerformingCaptions as $caption) {
            // Extract hashtags from caption
            preg_match_all('/#(\w+)/', $caption->caption_text, $matches);
            $hashtags = $matches[0] ?? [];
            
            foreach ($hashtags as $hashtag) {
                $this->updateOrCreateMLData([
                    'type' => 'hashtag',
                    'industry' => $caption->industry ?? 'general',
                    'platform' => $caption->platform ?? 'instagram',
                    'data' => $hashtag,
                    'performance_score' => $caption->engagement_rate,
                    'metadata' => [
                        'likes' => $caption->likes,
                        'comments' => $caption->comments,
                        'shares' => $caption->shares,
                    ],
                ]);
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Train keywords from successful captions
     */
    private function trainKeywords(): int
    {
        $count = 0;
        
        // Get captions with high engagement
        $highPerformingCaptions = CaptionAnalytics::whereNotNull('engagement_rate')
            ->where('engagement_rate', '>', 5.0)
            ->where('created_at', '>', now()->subDays(30))
            ->get();
        
        foreach ($highPerformingCaptions as $caption) {
            // Extract keywords (simple word frequency)
            $words = str_word_count(strtolower($caption->caption_text), 1);
            $keywords = array_count_values($words);
            
            // Get top keywords
            arsort($keywords);
            $topKeywords = array_slice($keywords, 0, 10, true);
            
            foreach ($topKeywords as $keyword => $frequency) {
                if (strlen($keyword) > 3) { // Skip short words
                    $this->updateOrCreateMLData([
                        'type' => 'keyword',
                        'industry' => $caption->industry ?? 'general',
                        'platform' => $caption->platform ?? 'instagram',
                        'data' => $keyword,
                        'performance_score' => $caption->engagement_rate,
                        'metadata' => [
                            'frequency' => $frequency,
                            'search_volume' => $frequency * 100, // Simulated
                        ],
                    ]);
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Train topics from trending captions
     */
    private function trainTopics(): int
    {
        $count = 0;
        
        // Get most engaging topics by industry
        $topicsByIndustry = CaptionAnalytics::select('industry', 'caption_text', 'engagement_rate')
            ->whereNotNull('engagement_rate')
            ->where('engagement_rate', '>', 5.0)
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('engagement_rate', 'desc')
            ->limit(100)
            ->get()
            ->groupBy('industry');
        
        foreach ($topicsByIndustry as $industry => $captions) {
            // Extract common topics (first sentence)
            foreach ($captions as $caption) {
                $sentences = explode('.', $caption->caption_text);
                $topic = trim($sentences[0] ?? '');
                
                if (!empty($topic) && strlen($topic) > 10) {
                    $this->updateOrCreateMLData([
                        'type' => 'topic',
                        'industry' => $industry,
                        'platform' => 'instagram',
                        'data' => $topic,
                        'performance_score' => $caption->engagement_rate,
                        'metadata' => [
                            'engagement_rate' => $caption->engagement_rate,
                        ],
                    ]);
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Train hooks from high-performing openings
     */
    private function trainHooks(): int
    {
        $count = 0;
        
        // Get captions with high engagement
        $highPerformingCaptions = CaptionAnalytics::whereNotNull('engagement_rate')
            ->where('engagement_rate', '>', 5.0)
            ->where('created_at', '>', now()->subDays(30))
            ->get();
        
        foreach ($highPerformingCaptions as $caption) {
            // Extract first line as hook
            $lines = explode("\n", $caption->caption_text);
            $hook = trim($lines[0] ?? '');
            
            if (!empty($hook) && strlen($hook) > 10) {
                $this->updateOrCreateMLData([
                    'type' => 'hook',
                    'industry' => $caption->industry ?? 'general',
                    'platform' => $caption->platform ?? 'instagram',
                    'data' => $hook,
                    'performance_score' => $caption->engagement_rate,
                    'metadata' => [
                        'tone' => $this->detectTone($hook),
                    ],
                ]);
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Train CTAs from successful captions
     */
    private function trainCTAs(): int
    {
        $count = 0;
        
        // Get captions with high engagement
        $highPerformingCaptions = CaptionAnalytics::whereNotNull('engagement_rate')
            ->where('engagement_rate', '>', 5.0)
            ->where('created_at', '>', now()->subDays(30))
            ->get();
        
        foreach ($highPerformingCaptions as $caption) {
            // Extract last line as CTA
            $lines = array_filter(explode("\n", $caption->caption_text));
            $cta = trim(end($lines) ?: '');
            
            if (!empty($cta) && strlen($cta) > 10) {
                $this->updateOrCreateMLData([
                    'type' => 'cta',
                    'industry' => $caption->industry ?? 'general',
                    'platform' => $caption->platform ?? 'instagram',
                    'data' => $cta,
                    'performance_score' => $caption->engagement_rate,
                    'metadata' => [
                        'goal' => $this->detectGoal($cta),
                    ],
                ]);
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Update or create ML data
     */
    private function updateOrCreateMLData(array $data): void
    {
        $existing = MLOptimizedData::where('type', $data['type'])
            ->where('industry', $data['industry'])
            ->where('data', $data['data'])
            ->first();
        
        if ($existing) {
            // Update performance score (weighted average)
            $newScore = ($existing->performance_score * 0.7) + ($data['performance_score'] * 0.3);
            $existing->update([
                'performance_score' => $newScore,
                'usage_count' => $existing->usage_count + 1,
                'last_trained_at' => now(),
                'metadata' => array_merge($existing->metadata ?? [], $data['metadata']),
            ]);
        } else {
            MLOptimizedData::create([
                'type' => $data['type'],
                'industry' => $data['industry'],
                'platform' => $data['platform'] ?? 'instagram',
                'data' => $data['data'],
                'performance_score' => $data['performance_score'],
                'usage_count' => 1,
                'is_active' => true,
                'last_trained_at' => now(),
                'metadata' => $data['metadata'],
            ]);
        }
    }
    
    /**
     * Clean low-performing data
     */
    private function cleanLowPerformingData(): void
    {
        // Deactivate data with score < 2.0 and not used in 30 days
        MLOptimizedData::where('performance_score', '<', 2.0)
            ->where('last_trained_at', '<', now()->subDays(30))
            ->update(['is_active' => false]);
    }
    
    /**
     * Detect tone from text
     */
    private function detectTone(string $text): string
    {
        $text = strtolower($text);
        
        if (preg_match('/\b(haha|wkwk|lol|lucu|ngakak)\b/', $text)) {
            return 'funny';
        }
        if (preg_match('/\b(beli|order|pesan|dapatkan|promo)\b/', $text)) {
            return 'persuasive';
        }
        if (preg_match('/\b(tips|cara|panduan|tutorial)\b/', $text)) {
            return 'educational';
        }
        
        return 'casual';
    }
    
    /**
     * Detect goal from CTA
     */
    private function detectGoal(string $text): string
    {
        $text = strtolower($text);
        
        if (preg_match('/\b(beli|order|pesan|checkout)\b/', $text)) {
            return 'closing';
        }
        if (preg_match('/\b(dm|chat|wa|whatsapp)\b/', $text)) {
            return 'engagement';
        }
        if (preg_match('/\b(follow|subscribe|like|share)\b/', $text)) {
            return 'branding';
        }
        
        return 'general';
    }
    
    /**
     * Log training results
     */
    private function logTraining(array $results, Carbon $startTime): void
    {
        MLTrainingLog::create([
            'trained_at' => $startTime,
            'duration_seconds' => now()->diffInSeconds($startTime),
            'hashtags_trained' => $results['hashtags'],
            'keywords_trained' => $results['keywords'],
            'topics_trained' => $results['topics'],
            'hooks_trained' => $results['hooks'],
            'ctas_trained' => $results['ctas'],
            'total_trained' => array_sum(array_filter($results, 'is_numeric')),
            'errors' => $results['errors'],
            'status' => empty($results['errors']) ? 'success' : 'partial',
        ]);
    }
}
