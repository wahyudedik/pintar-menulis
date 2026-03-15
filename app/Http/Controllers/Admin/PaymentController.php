<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $pendingPayments = Payment::with(['user', 'order'])
            ->where('status', 'processing')
            ->orderBy('created_at', 'desc')
            ->get();

        $verifiedPayments = Payment::with(['user', 'order'])
            ->where('status', 'success')
            ->orderBy('updated_at', 'desc')
            ->take(50)
            ->get();

        $rejectedPayments = Payment::with(['user', 'order'])
            ->where('status', 'failed')
            ->orderBy('updated_at', 'desc')
            ->take(50)
            ->get();

        $stats = [
            'pending' => Payment::where('status', 'processing')->count(),
            'verified_today' => Payment::where('status', 'success')
                ->whereDate('updated_at', today())
                ->count(),
            'total_verified' => Payment::where('status', 'success')->count(),
            'rejected' => Payment::where('status', 'failed')->count(),
        ];

        return view('admin.payments', compact('pendingPayments', 'verifiedPayments', 'rejectedPayments', 'stats'));
    }

    public function verify(Payment $payment)
    {
        if ($payment->status !== 'processing') {
            return back()->with('error', 'Payment sudah diproses sebelumnya');
        }

        // ESCROW: Update payment to verified and HOLD the money
        $payment->update([
            'status' => 'success',
            'escrow_status' => 'held', // Money is held by platform
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'paid_at' => now(),
        ]);

        // ESCROW: Update order status - NOW visible to operator
        $order = $payment->order;
        $order->update([
            'payment_status' => 'held', // Money is held, waiting for work completion
            'status' => 'pending', // NOW operator can see and accept this order
        ]);

        // Notify operator(s) - order is now available!
        if ($order->operator_id) {
            // Assigned to specific operator
            $this->notificationService->create(
                $order->operator,
                \App\Models\Notification::TYPE_ORDER_NEW,
                'Order Baru Tersedia!',
                "Ada order baru dengan budget Rp " . number_format($order->budget, 0, ',', '.') . " untuk kategori {$order->category}. Pembayaran sudah diterima dan di-hold platform.",
                route('operator.queue'),
                ['order_id' => $order->id]
            );

            $this->notificationService->sendEmail(
                $order->operator,
                'Order Baru Tersedia - Noteds',
                "Ada order baru dengan budget Rp " . number_format($order->budget, 0, ',', '.') . " untuk kategori {$order->category}. Pembayaran sudah diterima dan di-hold platform.",
                route('operator.queue'),
                'Lihat Order Queue'
            );
        } else {
            // No operator assigned — broadcast to all available operators
            $this->notificationService->notifyNewOrder($order);
        }

        // Notify client
        $this->notificationService->create(
            $payment->user,
            \App\Models\Notification::TYPE_PAYMENT_VERIFIED,
            'Pembayaran Diverifikasi',
            "Pembayaran untuk order #{$order->id} telah diverifikasi. Uang Anda di-hold platform dan akan diteruskan ke operator setelah order selesai dan Anda approve.",
            route('orders.show', $order)
        );

        $this->notificationService->sendEmail(
            $payment->user,
            'Pembayaran Diverifikasi - Noteds',
            "Pembayaran untuk order #{$order->id} telah diverifikasi. Uang Anda di-hold platform dan akan diteruskan ke operator setelah order selesai dan Anda approve.",
            route('orders.show', $order),
            'Lihat Order'
        );

        return back()->with('success', 'Payment berhasil diverifikasi! Uang di-hold platform. Order sekarang visible untuk operator.');
    }

    public function reject(Payment $payment)
    {
        if ($payment->status !== 'processing') {
            return back()->with('error', 'Payment sudah diproses sebelumnya');
        }

        $payment->update([
            'status' => 'failed',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        // Notify client
        $this->notificationService->create(
            $payment->user,
            \App\Models\Notification::TYPE_PAYMENT_REJECTED,
            'Pembayaran Ditolak',
            "Pembayaran untuk order #{$payment->order_id} ditolak. Silakan upload bukti pembayaran yang valid.",
            route('payment.show', $payment->order)
        );

        $this->notificationService->sendEmail(
            $payment->user,
            'Pembayaran Ditolak - Noteds',
            "Pembayaran untuk order #{$payment->order_id} ditolak. Silakan upload bukti pembayaran yang valid.",
            route('payment.show', $payment->order),
            'Upload Ulang Bukti'
        );

        return back()->with('success', 'Payment berhasil direject. Client akan dinotifikasi.');
    }
}
