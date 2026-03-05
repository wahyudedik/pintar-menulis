<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CaptionAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Stats
        $stats = [
            'total_captions' => CaptionAnalytics::where('user_id', $user->id)->count(),
            'avg_engagement' => CaptionAnalytics::where('user_id', $user->id)->avg('engagement_rate') ?? 0,
            'total_reach' => CaptionAnalytics::where('user_id', $user->id)->sum('reach'),
            'successful_captions' => CaptionAnalytics::where('user_id', $user->id)
                ->where('marked_as_successful', true)
                ->count(),
        ];
        
        // Top Performing Captions
        $topCaptions = CaptionAnalytics::where('user_id', $user->id)
            ->orderByDesc('engagement_rate')
            ->take(5)
            ->get();
        
        // Platform Performance
        $platformPerformance = CaptionAnalytics::where('user_id', $user->id)
            ->select('platform', DB::raw('AVG(engagement_rate) as avg_engagement'), DB::raw('COUNT(*) as count'))
            ->groupBy('platform')
            ->get();
        
        // Category Performance
        $categoryPerformance = CaptionAnalytics::where('user_id', $user->id)
            ->select('category', DB::raw('AVG(engagement_rate) as avg_engagement'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();
        
        // Engagement Over Time (Last 30 days)
        $engagementOverTime = CaptionAnalytics::where('user_id', $user->id)
            ->where('posted_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(posted_at) as date'), DB::raw('AVG(engagement_rate) as avg_engagement'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Recent Captions
        $recentCaptions = CaptionAnalytics::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        
        return view('client.analytics', compact(
            'stats',
            'topCaptions',
            'platformPerformance',
            'categoryPerformance',
            'engagementOverTime',
            'recentCaptions'
        ));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caption_text' => 'required|string',
            'category' => 'nullable|string',
            'subcategory' => 'nullable|string',
            'platform' => 'nullable|string',
            'tone' => 'nullable|string',
            'order_id' => 'nullable|exists:orders,id',
        ]);
        
        $analytics = CaptionAnalytics::create([
            'user_id' => auth()->id(),
            'caption_text' => $validated['caption_text'],
            'category' => $validated['category'] ?? null,
            'subcategory' => $validated['subcategory'] ?? null,
            'platform' => $validated['platform'] ?? null,
            'tone' => $validated['tone'] ?? null,
            'order_id' => $validated['order_id'] ?? null,
            'posted_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Caption saved for tracking',
            'analytics_id' => $analytics->id,
        ]);
    }
    
    public function update(Request $request, CaptionAnalytics $analytics)
    {
        // Check ownership
        if ($analytics->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
            'saves' => 'nullable|integer|min:0',
            'reach' => 'nullable|integer|min:0',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'user_rating' => 'nullable|integer|min:1|max:5',
            'user_notes' => 'nullable|string',
            'marked_as_successful' => 'nullable|boolean',
        ]);
        
        $analytics->update($validated);
        $analytics->updateEngagementRate();
        
        return response()->json([
            'success' => true,
            'message' => 'Analytics updated',
            'engagement_rate' => $analytics->engagement_rate,
        ]);
    }
    
    public function show(CaptionAnalytics $analytics)
    {
        // Check ownership
        if ($analytics->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'success' => true,
            'caption' => $analytics
        ]);
    }
    
    public function insights()
    {
        $user = auth()->user();
        
        // Best performing tone
        $bestTone = CaptionAnalytics::where('user_id', $user->id)
            ->select('tone', DB::raw('AVG(engagement_rate) as avg_engagement'))
            ->groupBy('tone')
            ->orderByDesc('avg_engagement')
            ->first();
        
        // Best performing platform
        $bestPlatform = CaptionAnalytics::where('user_id', $user->id)
            ->select('platform', DB::raw('AVG(engagement_rate) as avg_engagement'))
            ->groupBy('platform')
            ->orderByDesc('avg_engagement')
            ->first();
        
        // Best performing category
        $bestCategory = CaptionAnalytics::where('user_id', $user->id)
            ->select('category', DB::raw('AVG(engagement_rate) as avg_engagement'))
            ->groupBy('category')
            ->orderByDesc('avg_engagement')
            ->first();
        
        // Recommendations based on data
        $recommendations = [];
        
        if ($bestTone) {
            $recommendations[] = "Tone '{$bestTone->tone}' menghasilkan engagement terbaik ({$bestTone->avg_engagement}%). Gunakan lebih sering!";
        }
        
        if ($bestPlatform) {
            $recommendations[] = "Platform '{$bestPlatform->platform}' paling efektif untuk konten Anda.";
        }
        
        if ($bestCategory) {
            $recommendations[] = "Kategori '{$bestCategory->category}' mendapat respon terbaik dari audience.";
        }
        
        return response()->json([
            'success' => true,
            'insights' => [
                'best_tone' => $bestTone,
                'best_platform' => $bestPlatform,
                'best_category' => $bestCategory,
                'recommendations' => $recommendations,
            ]
        ]);
    }
    
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        
        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Stats
        $stats = [
            'total_captions' => CaptionAnalytics::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'avg_engagement' => CaptionAnalytics::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('engagement_rate') ?? 0,
            'total_reach' => CaptionAnalytics::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('reach'),
            'successful_captions' => CaptionAnalytics::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('marked_as_successful', true)
                ->count(),
        ];
        
        // Top Performing Captions
        $topCaptions = CaptionAnalytics::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('engagement_rate')
            ->take(10)
            ->get();
        
        // Platform Performance
        $platformPerformance = CaptionAnalytics::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('platform', DB::raw('AVG(engagement_rate) as avg_engagement'), DB::raw('COUNT(*) as count'))
            ->groupBy('platform')
            ->get();
        
        $data = [
            'user' => $user,
            'stats' => $stats,
            'topCaptions' => $topCaptions,
            'platformPerformance' => $platformPerformance,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
        
        $pdf = Pdf::loadView('client.analytics-pdf', $data);
        return $pdf->download('caption-analytics-' . now()->format('Y-m-d') . '.pdf');
    }
    
    public function exportCsv(Request $request)
    {
        $user = auth()->user();
        
        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $captions = CaptionAnalytics::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();
        
        $filename = 'caption-analytics-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($captions) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date',
                'Caption',
                'Platform',
                'Category',
                'Tone',
                'Likes',
                'Comments',
                'Shares',
                'Saves',
                'Reach',
                'Impressions',
                'Clicks',
                'Engagement Rate (%)',
                'Rating',
                'Successful'
            ]);
            
            // Data rows
            foreach ($captions as $caption) {
                fputcsv($file, [
                    $caption->posted_at ? $caption->posted_at->format('Y-m-d') : $caption->created_at->format('Y-m-d'),
                    substr($caption->caption_text, 0, 100),
                    $caption->platform ?? 'N/A',
                    $caption->category ?? 'N/A',
                    $caption->tone ?? 'N/A',
                    $caption->likes,
                    $caption->comments,
                    $caption->shares,
                    $caption->saves,
                    $caption->reach,
                    $caption->impressions,
                    $caption->clicks,
                    number_format($caption->engagement_rate, 2),
                    $caption->user_rating ?? 'N/A',
                    $caption->marked_as_successful ? 'Yes' : 'No'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
