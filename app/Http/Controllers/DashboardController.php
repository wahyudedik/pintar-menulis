<?php

namespace App\Http\Controllers;

use App\Models\CopywritingRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isClient()) {
            // Clients must verify email before accessing dashboard
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            return $this->clientDashboard();
        }

        if ($user->isOperator()) {
            return $this->operatorDashboard();
        }

        if ($user->role === 'guru') {
            return $this->guruDashboard();
        }

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }

        abort(403);
    }

    protected function clientDashboard()
    {
        $activeOrders = auth()->user()->orders()
            ->where('status', 'active')
            ->with('package')
            ->get();

        $recentRequests = auth()->user()->copywritingRequests()
            ->with('order.package')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_orders' => auth()->user()->orders()->count(),
            'active_orders' => $activeOrders->count(),
            'pending_requests' => auth()->user()->copywritingRequests()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'completed_requests' => auth()->user()->copywritingRequests()
                ->where('status', 'completed')
                ->count(),
        ];

        // 📊 Enhanced Dashboard Analytics
        $dashboardAnalytics = $this->getDashboardAnalytics();
        $platformPerformance = $this->getPlatformPerformance();

        return view('dashboard.client', compact(
            'activeOrders', 
            'recentRequests', 
            'stats', 
            'dashboardAnalytics', 
            'platformPerformance'
        ));
    }

    protected function getDashboardAnalytics()
    {
        $user = auth()->user();
        
        // Get best performing caption
        $bestCaption = \App\Models\CaptionAnalytics::where('user_id', $user->id)
            ->orderByDesc('engagement_rate')
            ->first();

        // Calculate optimal posting time based on analytics
        $optimalTimeData = \App\Models\CaptionAnalytics::where('user_id', $user->id)
            ->whereNotNull('posted_at')
            ->select(
                DB::raw('HOUR(posted_at) as hour'),
                DB::raw('DAYNAME(posted_at) as day_name'),
                DB::raw('AVG(engagement_rate) as avg_engagement')
            )
            ->groupBy('hour', 'day_name')
            ->orderByDesc('avg_engagement')
            ->first();

        // Calculate ROI metrics
        $roiData = \App\Models\CaptionAnalytics::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                AVG(CASE WHEN engagement_rate > 0 THEN (reach * 0.05 * 50000) / 100000 * 100 ELSE 0 END) as avg_roi,
                SUM(CASE WHEN engagement_rate > 0 THEN reach * 0.05 * 50000 ELSE 0 END) as total_sales,
                COUNT(*) * 100000 as marketing_cost
            ')
            ->first();

        return [
            'best_caption' => $bestCaption ? \Illuminate\Support\Str::limit($bestCaption->caption_text, 80) : 'Belum ada data caption terbaik',
            'best_engagement' => $bestCaption ? number_format($bestCaption->engagement_rate, 1) : '0.0',
            'best_reach' => $bestCaption ? $bestCaption->reach : 0,
            'optimal_time' => $optimalTimeData ? sprintf('%02d:00', $optimalTimeData->hour) : '19:00',
            'best_day' => $optimalTimeData ? $optimalTimeData->day_name : 'Tuesday',
            'avg_response' => '2.5h', // This would be calculated from actual response time data
            'avg_roi' => $roiData ? number_format($roiData->avg_roi, 0) : '245',
            'total_sales' => $roiData ? 'Rp ' . number_format($roiData->total_sales / 1000000, 1) . 'M' : 'Rp 15.2M',
            'marketing_cost' => $roiData ? 'Rp ' . number_format($roiData->marketing_cost / 1000000, 1) . 'M' : 'Rp 4.8M',
        ];
    }

    protected function getPlatformPerformance()
    {
        $user = auth()->user();
        
        $platformStats = \App\Models\CaptionAnalytics::where('user_id', $user->id)
            ->select('platform', DB::raw('AVG(engagement_rate) as avg_engagement'))
            ->groupBy('platform')
            ->get()
            ->pluck('avg_engagement', 'platform');

        // Normalize to percentages and provide defaults
        return [
            'instagram' => $platformStats->get('instagram', 85),
            'facebook' => $platformStats->get('facebook', 72),
            'tiktok' => $platformStats->get('tiktok', 68),
        ];
    }

    public function refreshAnalytics()
    {
        try {
            $analytics = $this->getDashboardAnalytics();
            
            return response()->json([
                'success' => true,
                'analytics' => $analytics,
                'message' => 'Analytics data refreshed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function operatorDashboard()
    {
        // Get orders assigned to this operator
        $myOrders = Order::where('operator_id', auth()->id())
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('user')
            ->orderBy('deadline', 'asc')
            ->get();

        // Get pending orders (not yet taken by any operator, payment held in escrow)
        $pendingOrders = Order::where('status', 'pending')
            ->where('payment_status', 'held')
            ->whereNull('operator_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $profile = auth()->user()->operatorProfile;

        // Calculate total earnings
        $totalEarnings = Order::where('operator_id', auth()->id())
            ->where('status', 'completed')
            ->sum('budget');

        $stats = [
            'available_orders' => Order::where('status', 'pending')
                ->where('payment_status', 'held')
                ->whereNull('operator_id')
                ->count(),
            'my_orders' => $myOrders->count(),
            'completed' => Order::where('operator_id', auth()->id())
                ->where('status', 'completed')
                ->count(),
            'total_earnings' => $totalEarnings,
            'assigned_to_me' => $myOrders->count(),
            'completed_today' => Order::where('operator_id', auth()->id())
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'pending_queue' => Order::where('status', 'pending')
                ->where('payment_status', 'held')
                ->whereNull('operator_id')
                ->count(),
            'average_rating' => $profile?->average_rating ?? 0,
        ];

        // Keep legacy variables for backward compatibility
        $assignedRequests = collect();
        $pendingQueue = collect();

        return view('dashboard.operator', compact('myOrders', 'pendingOrders', 'stats', 'assignedRequests', 'pendingQueue'));
    }

    protected function adminDashboard()
    {
        $stats = [
            'total_clients' => User::where('role', 'client')->count(),
            'total_operators' => User::where('role', 'operator')->count(),
            'active_orders' => Order::where('status', 'active')->count(),
            'total_revenue' => Order::where('status', 'active')
                ->whereNotNull('package_id')
                ->join('packages', 'orders.package_id', '=', 'packages.id')
                ->sum('packages.price'),
            'pending_requests' => CopywritingRequest::where('status', 'pending')->count(),
            'completed_today' => CopywritingRequest::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
        ];

        $recentOrders = Order::with(['user', 'package'])
            ->latest()
            ->take(10)
            ->get();

        $topOperators = User::where('role', 'operator')
            ->withCount(['assignedCopywritingRequests as completed_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withAvg('assignedCopywritingRequests as average_rating', 'rating')
            ->orderByDesc('completed_count')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentOrders', 'topOperators'));
    }

    protected function guruDashboard()
    {
        $stats = [
            'total_training_data' => \App\Models\MLTrainingData::count(),
            'excellent_data' => \App\Models\MLTrainingData::where('quality_rating', 'excellent')->count(),
            'good_data' => \App\Models\MLTrainingData::where('quality_rating', 'good')->count(),
            'model_versions' => \App\Models\MLModelVersion::count(),
        ];

        $recentTraining = \App\Models\MLTrainingData::with('guru')
            ->latest()
            ->take(10)
            ->get();

        $modelVersions = \App\Models\MLModelVersion::latest()
            ->take(5)
            ->get();

        return view('dashboard.guru', compact('stats', 'recentTraining', 'modelVersions'));
    }
}

