<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\NotificationService;

class OrderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    // Order History - List all orders
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['operator', 'package'])
            ->latest()
            ->get();
        
        $stats = [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'in_progress' => $orders->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed' => $orders->where('status', 'completed')->count(),
        ];
        
        return view('client.orders', compact('orders', 'stats'));
    }

    public function create(Package $package)
    {
        return view('orders.create', compact('package'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $package = Package::findOrFail($validated['package_id']);
        
        $order = Order::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'project_id' => $validated['project_id'] ?? null,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'status' => 'active',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Berhasil subscribe paket ' . $package->name);
    }

    // Order Detail
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $order->load(['operator', 'operator.operatorProfile', 'revisions']);
        // package is optional (freelance orders don't have one)
        if ($order->package_id) {
            $order->load('package');
        }
        
        return view('client.order-detail', compact('order'));
    }

    // Request Revision
    public function requestRevision(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Hanya bisa request revisi untuk order yang sudah completed');
        }

        $validated = $request->validate([
            'revision_notes' => 'required|string|min:20',
        ]);

        // Update revision_requested_at di revision terakhir
        $lastRevision = $order->revisions()->latest('revision_number')->first();
        if ($lastRevision) {
            $lastRevision->update([
                'revision_request' => $validated['revision_notes'],
                'revision_requested_at' => now(),
            ]);
        }

        $order->update([
            'status' => 'revision',
            'revision_notes' => $validated['revision_notes'],
        ]);

        // Send notification to operator
        $this->notificationService->notifyOrderRevision($order);

        return back()->with('success', 'Request revisi berhasil dikirim ke operator');
    }

    // Rate & Review
    public function rate(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Hanya bisa rate order yang sudah completed');
        }

        if ($order->rating) {
            return back()->with('error', 'Order ini sudah di-rate');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $order->update([
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        // Update operator profile stats
        if ($order->operator_id) {
            $profile = $order->operator->operatorProfile;
            if ($profile) {
                $allRatings = Order::where('operator_id', $order->operator_id)
                    ->whereNotNull('rating')
                    ->pluck('rating');
                
                $profile->update([
                    'average_rating' => $allRatings->avg(),
                    'total_reviews' => $allRatings->count(),
                ]);
            }
        }

        return back()->with('success', 'Terima kasih atas rating Anda!');
    }

    // ESCROW: Approve order and release payment to operator
    public function approve(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Hanya bisa approve order yang sudah completed');
        }

        if ($order->payment_status === 'released') {
            return back()->with('info', 'Order ini sudah di-approve sebelumnya');
        }

        // Update order status
        $order->update([
            'status' => 'approved',
            'payment_status' => 'released',
            'approved_at' => now(),
        ]);

        // ESCROW: Release payment to operator
        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'escrow_status' => 'released',
                'released_at' => now(),
            ]);

            // Calculate platform commission (dynamic, default 10%)
            $paymentAmount = $payment->amount;
            $commissionRate = cache('commission.order_rate', 10) / 100;
            $platformCommission = $paymentAmount * $commissionRate;
            $operatorEarnings = $paymentAmount * (1 - $commissionRate);

            // Add earnings to operator
            if ($order->operator_id) {
                $operatorProfile = $order->operator->operatorProfile;
                if ($operatorProfile && !$order->earnings_added) {
                    $operatorProfile->increment('total_earnings', $operatorEarnings);
                    $order->update(['earnings_added' => true]);

                    // Notify operator
                    $this->notificationService->create(
                        $order->operator,
                        \App\Models\Notification::TYPE_PAYMENT_RELEASED,
                        'Pembayaran Diterima!',
                        "Client telah approve order #{$order->id}. Penghasilan Rp " . number_format($operatorEarnings, 0, ',', '.') . " (setelah komisi 10%) telah masuk ke saldo Anda.",
                        route('operator.earnings'),
                        ['order_id' => $order->id]
                    );

                    // Send email to operator
                    $this->notificationService->sendEmail(
                        $order->operator,
                        'Pembayaran Diterima - Noteds',
                        "Client telah approve order #{$order->id}. Penghasilan Rp " . number_format($operatorEarnings, 0, ',', '.') . " (setelah komisi 10%) telah masuk ke saldo Anda.",
                        route('operator.earnings'),
                        'Lihat Penghasilan'
                    );
                }
            }
        }

        return back()->with('success', 'Order berhasil di-approve! Pembayaran telah diteruskan ke operator.');
    }

    // ESCROW: Dispute order
    public function dispute(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Hanya bisa dispute order yang sudah completed');
        }

        $validated = $request->validate([
            'dispute_reason' => 'required|string|min:20',
        ]);

        $order->update([
            'status' => 'disputed',
            'dispute_reason' => $validated['dispute_reason'],
        ]);

        // Notify admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->create(
                $admin,
                \App\Models\Notification::TYPE_ORDER_DISPUTED,
                'Order Disputed!',
                "Order #{$order->id} di-dispute oleh client. Alasan: {$validated['dispute_reason']}",
                route('admin.payments'),
                ['order_id' => $order->id]
            );

            $this->notificationService->sendEmail(
                $admin,
                'Order Disputed - Noteds',
                "Order #{$order->id} di-dispute oleh client. Alasan: {$validated['dispute_reason']}. Silakan mediasi segera.",
                route('admin.payments'),
                'Lihat Pembayaran'
            );
        }

        // Notify operator
        if ($order->operator_id) {
            $this->notificationService->create(
                $order->operator,
                \App\Models\Notification::TYPE_ORDER_DISPUTED,
                'Order Disputed',
                "Client dispute order #{$order->id}. Admin akan mediasi. Alasan: {$validated['dispute_reason']}",
                route('operator.workspace', $order),
                ['order_id' => $order->id]
            );

            $this->notificationService->sendEmail(
                $order->operator,
                'Order Disputed - Noteds',
                "Client dispute order #{$order->id}. Admin akan mediasi. Alasan: {$validated['dispute_reason']}.",
                route('operator.workspace', $order),
                'Lihat Workspace'
            );
        }

        return back()->with('success', 'Dispute berhasil diajukan. Admin akan segera mediasi.');
    }
}

