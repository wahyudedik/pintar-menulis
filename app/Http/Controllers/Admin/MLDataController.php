<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MLDataManagerService;
use App\Models\MLDataCache;
use Illuminate\Http\Request;

class MLDataController extends Controller
{
    private MLDataManagerService $mlManager;

    public function __construct(MLDataManagerService $mlManager)
    {
        $this->mlManager = $mlManager;
    }

    /**
     * Show ML data dashboard
     */
    public function index()
    {
        $stats = $this->mlManager->getMLStatistics();
        
        // Recent cache entries
        $recentEntries = MLDataCache::with([])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Platform performance
        $platformPerformance = MLDataCache::selectRaw('
                platform,
                COUNT(*) as total_profiles,
                AVG(data_quality_score) as avg_quality,
                SUM(api_calls_saved) as total_savings,
                AVG(cache_hit_count) as avg_hits
            ')
            ->groupBy('platform')
            ->orderBy('total_profiles', 'desc')
            ->get();

        // Daily cache hits (last 7 days)
        $dailyHits = MLDataCache::selectRaw('
                DATE(last_cache_hit) as date,
                COUNT(*) as hits,
                SUM(api_calls_saved) as savings
            ')
            ->where('last_cache_hit', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.ml-data.index', compact(
            'stats',
            'recentEntries', 
            'platformPerformance',
            'dailyHits'
        ));
    }

    /**
     * Show detailed ML entry
     */
    public function show(MLDataCache $mlData)
    {
        return view('admin.ml-data.show', compact('mlData'));
    }

    /**
     * Clean old ML data
     */
    public function cleanup(Request $request)
    {
        $deleted = $this->mlManager->cleanOldMLData();
        
        return response()->json([
            'success' => true,
            'message' => "Successfully cleaned {$deleted} old ML records",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Get ML statistics API
     */
    public function stats()
    {
        $stats = $this->mlManager->getMLStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Force refresh specific profile
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'platform' => 'required|string|in:instagram,tiktok,facebook,youtube,x,linkedin'
        ]);

        try {
            // Force fresh data fetch
            $result = $this->mlManager->getCompetitorData(
                $request->username, 
                $request->platform
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Profile refreshed successfully' : 'Failed to refresh profile',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing profile: ' . $e->getMessage()
            ], 500);
        }
    }
}