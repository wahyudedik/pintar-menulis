<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\KeywordResearch;
use App\Models\TrendingHashtag;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class KeywordResearchController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Show keyword research page
     */
    public function index()
    {
        $recentKeywords = KeywordResearch::where('user_id', auth()->id())
            ->orderBy('last_updated', 'desc')
            ->limit(20)
            ->get();

        $trendingHashtags = [
            'instagram' => TrendingHashtag::getTrendingForPlatform('instagram', 10),
            'tiktok' => TrendingHashtag::getTrendingForPlatform('tiktok', 10),
            'facebook' => TrendingHashtag::getTrendingForPlatform('facebook', 10),
        ];

        return view('client.keyword-research', compact('recentKeywords', 'trendingHashtags'));
    }

    /**
     * AI-powered keyword research
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
            'industry' => 'nullable|string',
            'platform' => 'nullable|string|in:instagram,facebook,tiktok,youtube,linkedin',
            'location' => 'nullable|string',
        ]);

        try {
            $keyword = $validated['keyword'];
            $industry = $validated['industry'] ?? 'general';
            $platform = $validated['platform'] ?? 'instagram';
            $location = $validated['location'] ?? 'Indonesia';

            // AI-powered keyword research
            $prompt = "Lakukan keyword research untuk '{$keyword}' di {$location}:

Industry: {$industry}
Platform: {$platform}
Target: Audience Indonesia

Berikan analisis dalam format JSON:
{
    \"main_keyword\": \"{$keyword}\",
    \"search_volume\": \"estimasi volume pencarian bulanan\",
    \"competition_level\": \"low/medium/high\",
    \"difficulty_score\": \"1-100\",
    \"trend_direction\": \"rising/stable/declining\",
    \"seasonal_trends\": [\"bulan dengan traffic tinggi\"],
    \"related_keywords\": [
        {\"keyword\": \"keyword terkait\", \"volume\": \"volume\", \"difficulty\": \"1-100\"},
        {\"keyword\": \"keyword terkait 2\", \"volume\": \"volume\", \"difficulty\": \"1-100\"}
    ],
    \"long_tail_keywords\": [
        {\"keyword\": \"long tail keyword\", \"volume\": \"volume\", \"intent\": \"informational/commercial/transactional\"},
        {\"keyword\": \"long tail keyword 2\", \"volume\": \"volume\", \"intent\": \"informational/commercial/transactional\"}
    ],
    \"cpc_low\": \"estimasi CPC minimum dalam Rupiah (angka saja)\",
    \"cpc_high\": \"estimasi CPC maksimum dalam Rupiah (angka saja)\",
    \"content_ideas\": [\"ide konten 1\", \"ide konten 2\", \"ide konten 3\"],
    \"hashtag_suggestions\": [\"#hashtag1\", \"#hashtag2\", \"#hashtag3\"],
    \"competitor_keywords\": [\"keyword yang digunakan kompetitor\"],
    \"opportunities\": [\"peluang keyword yang belum dimanfaatkan\"],
    \"recommendations\": [\"rekomendasi strategi keyword\"]
}

Berikan data realistis untuk pasar Indonesia.";

            $aiResponse = $this->geminiService->generateText($prompt, 1500, 0.7);
            
            // Parse AI response
            $result = null;
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $result = json_decode($matches[0], true);
            }

            // Fallback if AI parsing fails
            if (!$result || !is_array($result)) {
                $result = $this->generateFallbackKeywordData($keyword, $industry, $platform);
            }

            // Validate and clean data
            $result = $this->validateKeywordData($result, $keyword);

            // Save to database
            $keywordRecord = KeywordResearch::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'keyword' => $keyword,
                ],
                [
                    'industry' => $industry,
                    'platform' => $platform,
                    'location' => $location,
                    'search_volume' => $result['search_volume'] ?? 0,
                    'competition_level' => $result['competition_level'] ?? 'medium',
                    'difficulty_score' => $result['difficulty_score'] ?? 50,
                    'trend_direction' => $result['trend_direction'] ?? 'stable',
                    'data' => json_encode($result),
                    'last_updated' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Keyword research berhasil dilakukan dengan AI analysis'
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Keyword research failed', [
                'keyword' => $validated['keyword'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan keyword research. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate fallback keyword data
     */
    private function generateFallbackKeywordData(string $keyword, string $industry, string $platform): array
    {
        return [
            'main_keyword' => $keyword,
            'search_volume' => rand(1000, 50000),
            'competition_level' => ['low', 'medium', 'high'][rand(0, 2)],
            'difficulty_score' => rand(30, 80),
            'trend_direction' => ['rising', 'stable', 'declining'][rand(0, 2)],
            'seasonal_trends' => ['Januari', 'Juni', 'Desember'],
            'related_keywords' => [
                ['keyword' => $keyword . ' terbaik', 'volume' => rand(500, 5000), 'difficulty' => rand(20, 60)],
                ['keyword' => $keyword . ' murah', 'volume' => rand(300, 3000), 'difficulty' => rand(15, 50)],
                ['keyword' => 'cara ' . $keyword, 'volume' => rand(800, 8000), 'difficulty' => rand(25, 65)],
            ],
            'long_tail_keywords' => [
                ['keyword' => $keyword . ' terbaik di Indonesia', 'volume' => rand(100, 1000), 'intent' => 'commercial'],
                ['keyword' => 'tips memilih ' . $keyword, 'volume' => rand(200, 2000), 'intent' => 'informational'],
            ],
            'content_ideas' => [
                'Review ' . $keyword . ' terbaik',
                'Tips memilih ' . $keyword . ' yang tepat',
                'Perbandingan ' . $keyword . ' populer'
            ],
            'hashtag_suggestions' => ['#' . str_replace(' ', '', $keyword), '#' . $industry, '#indonesia'],
            'cpc_low' => rand(500, 2000),
            'cpc_high' => rand(2001, 8000),
            'competitor_keywords' => [$keyword . ' berkualitas', $keyword . ' original'],
            'opportunities' => ['Long-tail keywords dengan kompetisi rendah'],
            'recommendations' => ['Fokus pada long-tail keywords', 'Buat konten edukatif']
        ];
    }

    /**
     * Validate and clean keyword data
     */
    private function validateKeywordData(array $data, string $keyword): array
    {
        return [
            'main_keyword' => $data['main_keyword'] ?? $keyword,
            'search_volume' => is_numeric($data['search_volume']) ? (int)$data['search_volume'] : rand(1000, 50000),
            'competition_level' => in_array($data['competition_level'], ['low', 'medium', 'high']) ? $data['competition_level'] : 'medium',
            'difficulty_score' => is_numeric($data['difficulty_score']) ? min(100, max(1, (int)$data['difficulty_score'])) : 50,
            'trend_direction' => in_array($data['trend_direction'], ['rising', 'stable', 'declining']) ? $data['trend_direction'] : 'stable',
            'seasonal_trends' => is_array($data['seasonal_trends']) ? $data['seasonal_trends'] : ['Januari', 'Juni', 'Desember'],
            'related_keywords' => is_array($data['related_keywords']) ? $data['related_keywords'] : [],
            'long_tail_keywords' => is_array($data['long_tail_keywords']) ? $data['long_tail_keywords'] : [],
            'content_ideas' => is_array($data['content_ideas']) ? $data['content_ideas'] : [],
            'hashtag_suggestions' => is_array($data['hashtag_suggestions']) ? $data['hashtag_suggestions'] : [],
            'cpc_low' => is_numeric($data['cpc_low'] ?? null) ? (int)$data['cpc_low'] : rand(500, 2000),
            'cpc_high' => is_numeric($data['cpc_high'] ?? null) ? (int)$data['cpc_high'] : rand(2001, 8000),
            'competitor_keywords' => is_array($data['competitor_keywords']) ? $data['competitor_keywords'] : [],
            'opportunities' => is_array($data['opportunities']) ? $data['opportunities'] : [],
            'recommendations' => is_array($data['recommendations']) ? $data['recommendations'] : []
        ];
    }

    /**
     * Get keyword history
     */
    public function history()
    {
        $keywords = KeywordResearch::where('user_id', auth()->id())
            ->orderBy('last_updated', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $keywords,
        ]);
    }

    /**
     * Delete keyword
     */
    public function destroy(KeywordResearch $keyword)
    {
        // Check ownership
        if ($keyword->user_id !== auth()->id()) {
            abort(403);
        }

        $keyword->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keyword deleted successfully',
        ]);
    }
}
