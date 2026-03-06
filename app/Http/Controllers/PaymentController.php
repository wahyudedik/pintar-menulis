<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Show payment page for order
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // ESCROW: Allow payment for pending_payment orders
        if (!in_array($order->payment_status, ['pending_payment', 'refunded'])) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Order ini sudah dibayar');
        }

        // Check if already has pending payment
        $existingPayment = Payment::where('order_id', $order->id)
            ->whereIn('status', ['processing', 'success'])
            ->first();

        if ($existingPayment) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran sedang diproses admin');
        }

        $paymentSettings = PaymentSetting::where('is_active', true)->get();
        
        return view('client.payment', compact('order', 'paymentSettings'));
    }

    // Submit payment proof (manual transfer)
    public function submitProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'proof_image' => 'required|image|max:2048', // 2MB max
        ]);

        // Upload proof image
        $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');

        // Generate unique transaction ID for manual transfer
        $transactionId = 'MT-' . strtoupper(uniqid()) . '-' . time();

        Payment::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $transactionId,
            'amount' => $order->budget,
            'status' => 'processing', // Admin will verify
            'proof_image' => $proofPath,
            'payment_details' => [
                'bank' => $validated['payment_method'],
                'type' => 'manual_transfer',
            ],
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    // Midtrans Integration (Skeleton for future)
    public function midtransCheckout(Order $order)
    {
        // TODO: Implement Midtrans Snap API
        // 1. Create transaction
        // 2. Get snap token
        // 3. Return snap token to frontend
        
        return response()->json([
            'message' => 'Midtrans integration coming soon',
            'snap_token' => null,
        ]);
    }

    // Midtrans Webhook (Skeleton for future)
    public function midtransWebhook(Request $request)
    {
        // TODO: Implement Midtrans webhook handler
        // 1. Verify signature
        // 2. Update payment status
        // 3. Update order status if paid
        
        return response()->json(['status' => 'ok']);
    }
}

