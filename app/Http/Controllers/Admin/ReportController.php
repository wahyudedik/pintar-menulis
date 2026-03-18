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
        // Revenue Stats (from verified payments only)
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'success')
            ->whereMonth('verified_at', now()->month)
            ->sum('amount');

        // Withdrawal Stats
        $totalWithdrawals = WithdrawalRequest::where('status', 'completed')->sum('amount');
        $pendingWithdrawals = WithdrawalRequest::whereIn('status', ['pending', 'processing'])->sum('amount');

        // Detailed Withdrawal Stats
        $withdrawalStats = [
            'pending' => WithdrawalRequest::where('status', 'pending')->count(),
            'pendingAmount' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
            'processing' => WithdrawalRequest::where('status', 'processing')->count(),
            'processingAmount' => WithdrawalRequest::where('status', 'processing')->sum('amount'),
            'completed' => WithdrawalRequest::where('status', 'completed')->count(),
            'completedAmount' => WithdrawalRequest::where('status', 'completed')->sum('amount'),
            'rejected' => WithdrawalRequest::where('status', 'rejected')->count(),
            'rejectedAmount' => WithdrawalRequest::where('status', 'rejected')->sum('amount'),
        ];

        // Commission (Platform fee - dynamic rate from admin settings)
        $orderCommissionRate = cache('commission.order_rate', 10) / 100;
        $platformCommission = $totalRevenue * $orderCommissionRate;
        $operatorEarnings = $totalRevenue * (1 - $orderCommissionRate);

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

        // Revenue Over Time (Last 30 days) - from verified payments
        $revenueOverTime = Payment::select(
                DB::raw('DATE(verified_at) as date'),
                DB::raw('SUM(amount) as revenue')
            )
            ->where('status', 'success')
            ->where('verified_at', '>=', now()->subDays(30))
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

        return view('admin.reports', compact('stats', 'topOperators', 'revenueOverTime', 'ordersByCategory', 'withdrawalStats'));
    }
}
