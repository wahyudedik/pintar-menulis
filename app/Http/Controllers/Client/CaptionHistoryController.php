<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CaptionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaptionHistoryController extends Controller
{
    /**
     * Display caption history for machine learning
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Filters
        $category = $request->input('category');
        $platform = $request->input('platform');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        // Query
        $query = CaptionHistory::where('user_id', $user->id);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($platform) {
            $query->where('platform', $platform);
        }
        
        if ($dateFrom) {
            $query->where('last_generated_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('last_generated_at', '<=', $dateTo);
        }
        
        $histories = $query->orderByDesc('last_generated_at')
            ->paginate(20);
        
        // Stats
        $stats = [
            'total_generated' => CaptionHistory::where('user_id', $user->id)->count(),
            'total_unique' => CaptionHistory::where('user_id', $user->id)
                ->where('times_generated', 1)
                ->count(),
            'total_repeated' => CaptionHistory::where('user_id', $user->id)
                ->where('times_generated', '>', 1)
                ->count(),
            'last_7_days' => CaptionHistory::where('user_id', $user->id)
                ->where('last_generated_at', '>=', now()->subDays(7))
                ->count(),
        ];
        
        // Categories for filter
        $categories = CaptionHistory::where('user_id', $user->id)
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        // Platforms for filter
        $platforms = CaptionHistory::where('user_id', $user->id)
            ->select('platform')
            ->distinct()
            ->whereNotNull('platform')
            ->pluck('platform');
        
        // Generation frequency (for AI temperature info)
        $recentCount = CaptionHistory::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $aiTemperature = 0.7; // default
        if ($recentCount > 20) {
            $aiTemperature = 0.9;
        } elseif ($recentCount > 10) {
            $aiTemperature = 0.8;
        }
        
        return view('client.caption-history', compact(
            'histories',
            'stats',
            'categories',
            'platforms',
            'aiTemperature',
            'recentCount'
        ));
    }
    
    /**
     * Show single caption history detail
     */
    public function show(CaptionHistory $history)
    {
        // Check ownership
        if ($history->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }
    
    /**
     * Delete caption from history
     */
    public function destroy(CaptionHistory $history)
    {
        // Check ownership
        if ($history->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $history->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Caption deleted from history'
        ]);
    }
    
    /**
     * Clear all history (reset AI learning)
     */
    public function clearAll(Request $request)
    {
        $user = auth()->user();
        
        // Optional: only clear specific category/platform
        $category = $request->input('category');
        $platform = $request->input('platform');
        
        $query = CaptionHistory::where('user_id', $user->id);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($platform) {
            $query->where('platform', $platform);
        }
        
        $count = $query->count();
        $query->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted {$count} caption(s) from history. AI learning has been reset."
        ]);
    }
}
