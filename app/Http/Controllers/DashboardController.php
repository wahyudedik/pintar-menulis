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

        return view('dashboard.client', compact('activeOrders', 'recentRequests', 'stats'));
    }

    protected function operatorDashboard()
    {
        // Get orders assigned to this operator
        $myOrders = Order::where('operator_id', auth()->id())
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('user')
            ->orderBy('deadline', 'asc')
            ->get();

        // Get pending orders (not yet taken by any operator)
        $pendingOrders = Order::where('status', 'pending')
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
                ->whereNull('operator_id')
                ->count(),
            'average_rating' => $profile->average_rating ?? 0,
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

