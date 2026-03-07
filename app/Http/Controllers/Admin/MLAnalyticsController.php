<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaptionHistory;
use Illuminate\Support\Facades\DB;

class MLAnalyticsController extends Controller
{
    /**
     * ML Analytics Dashboard
     */
    public function index()
    {
        // Overall stats
        $totalCaptions = CaptionHistory::count();
        $totalRated = CaptionHistory::whereNotNull('rating')->count();
        $avgRating = CaptionHistory::whereNotNull('rating')->avg('rating');
        $ratingRate = $totalCaptions > 0 ? ($totalRated / $totalCaptions * 100) : 0;
        
        // Top performing words per language
        $topWordsByLanguage = $this->analyzeTopWords();
        
        // Category performance
        $categoryPerformance = CaptionHistory::whereNotNull('rating')
            ->selectRaw('category, AVG(rating) as avg_rating, COUNT(*) as total, 
                        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as high_rated')
            ->groupBy('category')
            ->orderBy('avg_rating', 'desc')
            ->get();
        
        // Platform performance
        $platformPerformance = CaptionHistory::whereNotNull('rating')
            ->selectRaw('platform, AVG(rating) as avg_rating, COUNT(*) as total,
                        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as high_rated')
            ->groupBy('platform')
            ->orderBy('avg_rating', 'desc')
            ->get();
        
        // Tone performance
        $tonePerformance = CaptionHistory::whereNotNull('rating')
            ->selectRaw('tone, AVG(rating) as avg_rating, COUNT(*) as total,
                        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as high_rated')
            ->groupBy('tone')
            ->orderBy('avg_rating', 'desc')
            ->get();
        
        // Local language performance
        $localLanguagePerformance = CaptionHistory::whereNotNull('rating')
            ->whereNotNull('local_language')
            ->selectRaw('local_language, AVG(rating) as avg_rating, COUNT(*) as total,
                        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as high_rated')
            ->groupBy('local_language')
            ->orderBy('avg_rating', 'desc')
            ->get();
        
        // Rating distribution
        $ratingDistribution = CaptionHistory::whereNotNull('rating')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
        
        // Auto-recommendations
        $recommendations = $this->generateRecommendations();
        
        // Recent feedback
        $recentFeedback = CaptionHistory::whereNotNull('feedback')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['user_id', 'caption_text', 'rating', 'feedback', 'category', 'platform', 'created_at']);
        
        return view('admin.ml-analytics.index', compact(
            'totalCaptions',
            'totalRated',
            'avgRating',
            'ratingRate',
            'topWordsByLanguage',
            'categoryPerformance',
            'platformPerformance',
            'tonePerformance',
            'localLanguagePerformance',
            'ratingDistribution',
            'recommendations',
            'recentFeedback'
        ));
    }
    
    /**
     * Analyze top performing words per language
     */
    private function analyzeTopWords()
    {
        $languages = ['jawa', 'sunda', 'betawi', 'minang', 'batak'];
        $results = [];
        
        foreach ($languages as $lang) {
            // Get high-rated captions with this language
            $captions = CaptionHistory::where('local_language', $lang)
                ->where('rating', '>=', 4)
                ->pluck('caption_text');
            
            if ($captions->isEmpty()) {
                continue;
            }
            
            // Define keywords for each language
            $keywords = $this->getLanguageKeywords($lang);
            
            // Count occurrences
            $wordCounts = [];
            foreach ($keywords as $keyword) {
                $count = 0;
                foreach ($captions as $caption) {
                    if (stripos($caption, $keyword) !== false) {
                        $count++;
                    }
                }
                if ($count > 0) {
                    $wordCounts[$keyword] = $count;
                }
            }
            
            // Sort by count
            arsort($wordCounts);
            
            $results[$lang] = [
                'total_captions' => $captions->count(),
                'top_words' => array_slice($wordCounts, 0, 10, true)
            ];
        }
        
        return $results;
    }
    
    /**
     * Get keywords for each language
     */
    private function getLanguageKeywords($language)
    {
        $keywords = [
            'jawa' => ['monggo', 'murah pol', 'enak tenan', 'apik', 'ojo nganti', 'rek', 'piye', 'lho kok'],
            'sunda' => ['mangga', 'murah pisan', 'saé', 'teu meunang', 'atuh', 'euy', 'nuhun', 'kumaha'],
            'betawi' => ['aye', 'gue', 'elu', 'lu', 'kagak', 'kaga', 'nih ye', 'kece badai', 'mantep jiwa', 'jangan sampe'],
            'minang' => ['lah', 'bana', 'ndak', 'rancak', 'lamak', 'uni', 'uda', 'alun', 'beko', 'ciek'],
            'batak' => ['horas', 'lae', 'ito', 'eda', 'sai', 'tung mansai', 'hatop', 'boasa', 'nunga', 'dang']
        ];
        
        return $keywords[$language] ?? [];
    }
    
    /**
     * Generate auto-recommendations for prompt improvement
     */
    private function generateRecommendations()
    {
        $recommendations = [];
        
        // Check if any category has low average rating
        $lowRatedCategories = CaptionHistory::whereNotNull('rating')
            ->selectRaw('category, AVG(rating) as avg_rating, COUNT(*) as count')
            ->groupBy('category')
            ->having('avg_rating', '<', 3.5)
            ->having('count', '>=', 10)
            ->get();
        
        foreach ($lowRatedCategories as $cat) {
            $recommendations[] = [
                'priority' => 'high',
                'type' => 'category_improvement',
                'title' => 'Improve Category: ' . $cat->category,
                'message' => sprintf(
                    'Category "%s" has low average rating (%.1f/5) from %d captions. Review and update prompt for this category.',
                    $cat->category,
                    $cat->avg_rating,
                    $cat->count
                ),
                'action' => 'Review prompt for ' . $cat->category
            ];
        }
        
        // Check if local language has high rating
        $highRatedLanguages = CaptionHistory::whereNotNull('rating')
            ->whereNotNull('local_language')
            ->selectRaw('local_language, AVG(rating) as avg_rating, COUNT(*) as count')
            ->groupBy('local_language')
            ->having('avg_rating', '>=', 4.0)
            ->having('count', '>=', 10)
            ->get();
        
        foreach ($highRatedLanguages as $lang) {
            $recommendations[] = [
                'priority' => 'medium',
                'type' => 'language_success',
                'title' => 'Success: Bahasa ' . ucfirst($lang->local_language),
                'message' => sprintf(
                    'Bahasa %s performing well (%.1f/5 from %d captions). Current prompt is effective!',
                    ucfirst($lang->local_language),
                    $lang->avg_rating,
                    $lang->count
                ),
                'action' => 'Keep current approach'
            ];
        }
        
        // Check rating rate
        $totalCaptions = CaptionHistory::count();
        $totalRated = CaptionHistory::whereNotNull('rating')->count();
        $ratingRate = $totalCaptions > 0 ? ($totalRated / $totalCaptions * 100) : 0;
        
        if ($ratingRate < 20 && $totalCaptions > 50) {
            $recommendations[] = [
                'priority' => 'medium',
                'type' => 'engagement',
                'title' => 'Low Rating Participation',
                'message' => sprintf(
                    'Only %.1f%% of captions are rated. Consider adding incentives or making rating more prominent.',
                    $ratingRate
                ),
                'action' => 'Improve rating UI/UX'
            ];
        }
        
        // Check if enough data for ML
        if ($totalRated >= 500) {
            $recommendations[] = [
                'priority' => 'high',
                'type' => 'ml_ready',
                'title' => 'Ready for ML Fine-tuning',
                'message' => sprintf(
                    'You have %d rated captions! This is enough data to start fine-tuning the model.',
                    $totalRated
                ),
                'action' => 'Export data for fine-tuning'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Export training data for ML
     */
    public function exportTrainingData()
    {
        // Get high-rated captions (4-5 stars)
        $trainingData = CaptionHistory::where('rating', '>=', 4)
            ->get(['caption_text', 'category', 'platform', 'tone', 'local_language', 'rating'])
            ->map(function ($item) {
                return [
                    'input' => [
                        'category' => $item->category,
                        'platform' => $item->platform,
                        'tone' => $item->tone,
                        'local_language' => $item->local_language
                    ],
                    'output' => $item->caption_text,
                    'score' => $item->rating / 5.0 // Normalize to 0-1
                ];
            });
        
        $filename = 'training_data_' . date('Y-m-d') . '.json';
        $path = storage_path('app/ml-exports/' . $filename);
        
        // Create directory if not exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        file_put_contents($path, json_encode($trainingData, JSON_PRETTY_PRINT));
        
        return response()->download($path)->deleteFileAfterSend(false);
    }
}
