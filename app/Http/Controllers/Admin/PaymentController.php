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

        $payment->update([
            'status' => 'success',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        // Update order status to paid
        $order = $payment->order;
        $order->update(['payment_status' => 'paid']);

        // Notify client
        $this->notificationService->create(
            $payment->user_id,
            'payment_verified',
            'Pembayaran Diverifikasi',
            "Pembayaran untuk order #{$order->id} telah diverifikasi. Terima kasih!",
            route('orders.show', $order)
        );

        // Notify operator (payment will be released after review period)
        if ($order->operator_id) {
            $this->notificationService->create(
                $order->operator_id,
                'payment_received',
                'Pembayaran Diterima',
                "Client telah membayar order #{$order->id}. Penghasilan akan masuk ke saldo Anda.",
                route('operator.earnings')
            );
        }

        return back()->with('success', 'Payment berhasil diverifikasi!');
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
            $payment->user_id,
            'payment_rejected',
            'Pembayaran Ditolak',
            "Pembayaran untuk order #{$payment->order_id} ditolak. Silakan upload bukti pembayaran yang valid.",
            route('payment.show', $payment->order)
        );

        return back()->with('success', 'Payment berhasil direject. Client akan dinotifikasi.');
    }
}
