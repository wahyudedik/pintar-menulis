<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OperatorProfile;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class OrderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Order Queue - Lihat semua incoming orders
    public function queue()
    {
        // ESCROW: Only show orders with payment_status='held' (payment verified and held by platform)
        $orders = Order::where('status', 'pending')
            ->where('payment_status', 'held') // ESCROW: Only paid orders
            ->where(function($query) {
                // Show orders without operator OR assigned to me
                $query->whereNull('operator_id')
                      ->orWhere('operator_id', auth()->id());
            })
            ->with(['user', 'operator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $myOrders = Order::where('operator_id', auth()->id())
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with(['user'])
            ->orderBy('deadline', 'asc')
            ->get();

        return view('operator.queue', compact('orders', 'myOrders'));
    }

    // Accept Order
    public function accept(Order $order)
    {
        if ($order->operator_id !== null) {
            return back()->with('error', 'Order sudah diambil operator lain');
        }

        $order->update([
            'operator_id' => auth()->id(),
            'status' => 'accepted',
        ]);

        // Send notification to client
        $this->notificationService->notifyOrderAccepted($order);

        return redirect()->route('operator.workspace', $order)
            ->with('success', 'Order berhasil diterima! Silakan kerjakan.');
    }

    // Reject Order
    public function reject(Order $order)
    {
        if ($order->operator_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak bisa reject order ini');
        }

        $order->update([
            'operator_id' => null,
            'status' => 'pending',
        ]);

        // Send notification to client
        $this->notificationService->notifyOrderRejected($order);

        return redirect()->route('operator.queue')
            ->with('success', 'Order berhasil ditolak');
    }

    // Workspace - Tempat mengerjakan
    public function workspace(Order $order)
    {
        if ($order->operator_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('operator.workspace', compact('order'));
    }

    // Submit Work
    public function submit(Request $request, Order $order)
    {
        if ($order->operator_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'result' => 'required|string|min:50',
            'notes' => 'nullable|string',
        ]);

        // Hitung revision number
        $revisionNumber = $order->revisions()->count() + 1;

        // Simpan ke order_revisions untuk history
        \App\Models\OrderRevision::create([
            'order_id' => $order->id,
            'revision_number' => $revisionNumber,
            'result' => $validated['result'],
            'operator_notes' => $validated['notes'] ?? null,
            'revision_request' => $order->revision_notes, // Simpan request revisi sebelumnya jika ada
            'submitted_at' => now(),
        ]);

        // Update order dengan hasil terbaru
        $order->update([
            'result' => $validated['result'],
            'operator_notes' => $validated['notes'] ?? null,
            'status' => 'completed',
            'completed_at' => now(),
            'revision_notes' => null, // Clear revision notes setelah dikerjakan
        ]);

        // Update operator stats
        $profile = auth()->user()->operatorProfile;
        if ($profile && $revisionNumber === 1) {
            // Only increment completed orders count, not earnings
            // Earnings will be added when payment is verified by admin
            $profile->increment('completed_orders');
        }

        // Send notification to client
        $this->notificationService->notifyOrderCompleted($order);

        return redirect()->route('operator.queue')
            ->with('success', 'Pekerjaan berhasil disubmit! Client akan mereview.');
    }

    // Earnings Dashboard
    public function earnings()
    {
        $profile = auth()->user()->operatorProfile;
        
        $completedOrders = Order::where('operator_id', auth()->id())
            ->where('status', 'completed')
            ->with('user')
            ->orderBy('completed_at', 'desc')
            ->get();

        // Calculate available balance (total earnings - pending withdrawals)
        $pendingWithdrawals = \App\Models\WithdrawalRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $totalEarnings = $profile->total_earnings ?? 0;
        $availableBalance = $totalEarnings - $pendingWithdrawals;

        $stats = [
            'total_earnings' => $totalEarnings,
            'completed_orders' => $profile->completed_orders ?? 0,
            'average_rating' => $profile->average_rating ?? 0,
            'pending_withdrawal' => $availableBalance,
        ];

        return view('operator.earnings', compact('stats', 'completedOrders'));
    }
}
