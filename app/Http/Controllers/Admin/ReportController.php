<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Revenue Stats
        $totalRevenue = Order::where('status', 'completed')->sum('budget');
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->sum('budget');

        // Withdrawal Stats
        $totalWithdrawals = WithdrawalRequest::where('status', 'completed')->sum('amount');
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->sum('amount');

        // Commission (Platform fee - 10% of revenue)
        $platformCommission = $totalRevenue * 0.1;
        $operatorEarnings = $totalRevenue * 0.9;

        // Order Stats
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Top Operators
        $topOperators = User::where('role', 'operator')
            ->withCount(['operatorOrders as completed_orders' => function($query) {
                $query->where('status', 'completed');
            }])
            ->with('operatorProfile')
            ->orderByDesc('completed_orders')
            ->take(10)
            ->get();

        // Revenue Over Time (Last 30 days)
        $revenueOverTime = Order::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(budget) as revenue')
            )
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Orders by Category
        $ordersByCategory = Order::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        $stats = compact(
            'totalRevenue',
            'monthlyRevenue',
            'totalWithdrawals',
            'pendingWithdrawals',
            'platformCommission',
            'operatorEarnings',
            'totalOrders',
            'completedOrders',
            'pendingOrders'
        );

        return view('admin.reports', compact('stats', 'topOperators', 'revenueOverTime', 'ordersByCategory'));
    }
}
