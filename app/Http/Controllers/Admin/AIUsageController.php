<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CaptionHistory;
use App\Models\CaptionAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIUsageController extends Controller
{
    /**
     * Display AI usage overview for all users
     */
    public function index()
    {
        // Overall Stats
        $stats = [
            'total_generations' => CaptionHistory::count(),
            'total_users' => User::where('role', 'client')->count(),
            'active_users_30d' => CaptionHistory::where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')
                ->count('user_id'),
            'avg_per_user' => CaptionHistory::select('user_id', DB::raw('COUNT(*) as count'))
                ->groupBy('user_id')
                ->get()
                ->avg('count'),
            'generations_today' => CaptionHistory::whereDate('created_at', today())->count(),
            'generations_this_week' => CaptionHistory::where('created_at', '>=', now()->startOfWeek())->count(),
            'generations_this_month' => CaptionHistory::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
        
        // Top Users by Generation Count
        $topUsers = CaptionHistory::select('user_id', DB::raw('COUNT(*) as total_generations'))
            ->groupBy('user_id')
            ->orderByDesc('total_generations')
            ->limit(10)
            ->with('user:id,name,email')
            ->get()
            ->map(function($item) {
                return [
                    'user' => $item->user,
                    'total_generations' => $item->total_generations,
                    'last_generated' => CaptionHistory::where('user_id', $item->user_id)
                        ->max('last_generated_at'),
                ];
            });
        
        // Platform Distribution
        $platformStats = CaptionHistory::select('platform', DB::raw('COUNT(*) as count'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->get();
        
        // Category Distribution
        $categoryStats = CaptionHistory::select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();
        
        // Daily Generations (Last 30 Days)
        $dailyGenerations = CaptionHistory::where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // User Growth (Users who generated for first time)
        $userGrowth = CaptionHistory::select('user_id', DB::raw('MIN(created_at) as first_generation'))
            ->groupBy('user_id')
            ->having('first_generation', '>=', now()->subDays(30))
            ->get()
            ->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->first_generation)->format('Y-m-d');
            })
            ->map(function($items) {
                return $items->count();
            });
        
        return view('admin.ai-usage.index', compact(
            'stats',
            'topUsers',
            'platformStats',
            'categoryStats',
            'dailyGenerations',
            'userGrowth'
        ));
    }
    
    /**
     * Display AI usage for specific user
     */
    public function show(User $user)
    {
        // Check if user is client
        if ($user->role !== 'client') {
            return redirect()->route('admin.ai-usage.index')
                ->with('error', 'AI usage only available for client users');
        }
        
        // User Stats
        $stats = [
            'total_generations' => CaptionHistory::where('user_id', $user->id)->count(),
            'unique_captions' => CaptionHistory::where('user_id', $user->id)
                ->where('times_generated', 1)
                ->count(),
            'repeated_captions' => CaptionHistory::where('user_id', $user->id)
                ->where('times_generated', '>', 1)
                ->count(),
            'first_generation' => CaptionHistory::where('user_id', $user->id)
                ->min('created_at'),
            'last_generation' => CaptionHistory::where('user_id', $user->id)
                ->max('last_generated_at'),
            'generations_7d' => CaptionHistory::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'generations_30d' => CaptionHistory::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
        ];
        
        // Calculate AI Temperature
        $recentCount = CaptionHistory::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $aiTemperature = 0.7;
        if ($recentCount > 20) {
            $aiTemperature = 0.9;
        } elseif ($recentCount > 10) {
            $aiTemperature = 0.8;
        }
        
        $stats['ai_temperature'] = $aiTemperature;
        $stats['temperature_level'] = $aiTemperature == 0.9 ? 'Very Creative (Power User)' : 
                                      ($aiTemperature == 0.8 ? 'More Creative (Frequent User)' : 'Balanced (Default)');
        
        // Platform Usage
        $platformUsage = CaptionHistory::where('user_id', $user->id)
            ->select('platform', DB::raw('COUNT(*) as count'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->get();
        
        // Category Usage
        $categoryUsage = CaptionHistory::where('user_id', $user->id)
            ->select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();
        
        // Tone Usage
        $toneUsage = CaptionHistory::where('user_id', $user->id)
            ->select('tone', DB::raw('COUNT(*) as count'))
            ->whereNotNull('tone')
            ->groupBy('tone')
            ->orderByDesc('count')
            ->get();
        
        // Generation Timeline (Last 30 Days)
        $timeline = CaptionHistory::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Recent Generations
        $recentGenerations = CaptionHistory::where('user_id', $user->id)
            ->orderByDesc('last_generated_at')
            ->limit(10)
            ->get();
        
        // Analytics Integration (if user tracks performance)
        $analyticsStats = [
            'tracked_captions' => CaptionAnalytics::where('user_id', $user->id)->count(),
            'avg_engagement' => CaptionAnalytics::where('user_id', $user->id)->avg('engagement_rate') ?? 0,
            'successful_captions' => CaptionAnalytics::where('user_id', $user->id)
                ->where('marked_as_successful', true)
                ->count(),
        ];
        
        // Usage Frequency Analysis
        $daysSinceFirst = $stats['first_generation'] ? 
            now()->diffInDays($stats['first_generation']) : 0;
        $avgPerDay = $daysSinceFirst > 0 ? 
            round($stats['total_generations'] / $daysSinceFirst, 2) : 0;
        
        $stats['days_since_first'] = $daysSinceFirst;
        $stats['avg_per_day'] = $avgPerDay;
        
        return view('admin.ai-usage.show', compact(
            'user',
            'stats',
            'platformUsage',
            'categoryUsage',
            'toneUsage',
            'timeline',
            'recentGenerations',
            'analyticsStats'
        ));
    }
    
    /**
     * Get AI usage stats for user list (AJAX)
     */
    public function userStats(User $user)
    {
        $stats = [
            'total_generations' => CaptionHistory::where('user_id', $user->id)->count(),
            'last_generated' => CaptionHistory::where('user_id', $user->id)
                ->max('last_generated_at'),
            'generations_7d' => CaptionHistory::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
        
        return response()->json($stats);
    }
}
