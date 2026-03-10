<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\GoogleAdsService;
use App\Models\KeywordResearch;
use App\Models\TrendingHashtag;
use Illuminate\Http\Request;

class KeywordResearchController extends Controller
{
    protected $googleAds;

    public function __construct(GoogleAdsService $googleAds)
    {
        $this->googleAds = $googleAds;
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
     * Search keyword
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
        ]);

        try {
            $result = $this->googleAds->getKeywordIdeas($validated['keyword']);
            
            // Save to database
            $keyword = $this->googleAds->saveKeywordResearch(auth()->id(), $result);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            \Log::error('Keyword search failed', [
                'keyword' => $validated['keyword'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan keyword research. Silakan coba lagi.',
            ], 500);
        }
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
