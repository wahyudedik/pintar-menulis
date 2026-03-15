<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserSubscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Halaman pricing publik
    public function pricing()
    {
        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        $user     = Auth::user();
        $current  = $user?->currentSubscription()?->load('package');

        return view('client.subscription.pricing', compact('packages', 'user', 'current'));
    }

    // Halaman dashboard subscription user
    public function index()
    {
        $user    = Auth::user();
        $current = $user->currentSubscription()?->load('package');
        $history = $user->subscriptions()->with('package')->latest()->get();
        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();

        return view('client.subscription.index', compact('current', 'history', 'packages'));
    }

    // Mulai trial
    public function startTrial(Request $request, Package $package)
    {
        $user = Auth::user();

        if ($user->hasUsedTrial()) {
            return back()->with('error', 'Anda sudah pernah menggunakan masa trial sebelumnya.');
        }

        if (!$package->has_trial) {
            return back()->with('error', 'Paket ini tidak memiliki masa trial.');
        }

        UserSubscription::startTrial($user, $package);

        // Notify user
        $this->notificationService->notifySubscriptionActivated(
            $user->currentSubscription()->load('package', 'user')
        );

        return redirect()->route('subscription.index')
            ->with('success', "🎉 Trial {$package->name} aktif! Nikmati 30 hari gratis.");
    }

    // Halaman checkout
    public function checkout(Request $request, Package $package)
    {
        $user         = Auth::user();
        $billingCycle = $request->get('billing', 'monthly');
        $price        = $billingCycle === 'yearly'
            ? ($package->yearly_price ?? $package->price * 10)
            : $package->price;

        $manualBanks     = \App\Models\PaymentSetting::manual()->where('is_active', true)->get();
        $midtrans        = \App\Models\PaymentSetting::getMidtrans();
        $xendit          = \App\Models\PaymentSetting::getXendit();
        $enabledGateways = \App\Models\PaymentSetting::getEnabledGateways();

        return view('client.subscription.checkout', compact(
            'package', 'billingCycle', 'price', 'user',
            'manualBanks', 'midtrans', 'xendit', 'enabledGateways'
        ));
    }

    // Proses pembayaran (manual transfer)
    public function processPayment(Request $request, Package $package)
    {
        $request->validate([
            'billing_cycle'  => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
            'payment_proof'  => 'required_if:gateway,manual_transfer|nullable|image|max:2048',
            'gateway'        => 'required|in:manual_transfer,midtrans,xendit',
        ]);

        $user = Auth::user();

        // For gateway payments (Midtrans/Xendit), redirect to gateway
        if ($request->gateway !== 'manual_transfer') {
            // TODO: integrate actual gateway SDK — for now create pending
        }

        // Simpan bukti bayar
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        // Cancel existing pending
        UserSubscription::where('user_id', $user->id)
            ->where('status', 'pending_payment')
            ->update(['status' => 'cancelled']);

        $newSub = UserSubscription::create([
            'user_id'           => $user->id,
            'package_id'        => $package->id,
            'status'            => 'pending_payment',
            'billing_cycle'     => $request->billing_cycle,
            'trial_used'        => true,
            'ai_quota_limit'    => $package->ai_quota_monthly,
            'ai_quota_used'     => 0,
            'payment_method'    => $request->payment_method,
            'payment_reference' => $proofPath,
        ]);

        $this->notificationService->notifySubscriptionPendingVerification($newSub->load('package'));

        return redirect()->route('subscription.index')
            ->with('success', '✅ Bukti pembayaran berhasil dikirim! Admin akan verifikasi dalam 1×24 jam.');    }

    // Cancel subscription
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $sub  = $user->currentSubscription();

        if (!$sub) {
            return back()->with('error', 'Tidak ada subscription aktif.');
        }

        $sub->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription berhasil dibatalkan. Akses tetap aktif hingga periode berakhir.');
    }

    // Admin: verifikasi pembayaran
    public function adminVerify(Request $request, UserSubscription $subscription)
    {
        $billingCycle = $subscription->billing_cycle ?? 'monthly';
        $months       = $billingCycle === 'yearly' ? 12 : 1;

        $subscription->update([
            'status'         => 'active',
            'starts_at'      => now(),
            'ends_at'        => now()->addMonths($months),
            'ai_quota_used'  => 0, // reset quota on activation
            'quota_reset_at' => now()->addMonth(),
        ]);

        // Trigger referral commission jika applicable (hanya sekali per user)
        app(\App\Services\ReferralService::class)->convertSubscription($subscription->fresh());

        $this->notificationService->notifySubscriptionActivated($subscription->fresh()->load('package', 'user'));

        return back()->with('success', "✅ Subscription #{$subscription->id} ({$subscription->user->name}) berhasil diaktifkan.");
    }

    // Admin: tolak pembayaran
    public function adminReject(Request $request, UserSubscription $subscription)
    {
        $subscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        $this->notificationService->notifySubscriptionRejected($subscription->load('package', 'user'));

        return back()->with('success', "Subscription #{$subscription->id} ditolak.");
    }

    // ── Webhooks ──────────────────────────────────────────────────────────────

    // Midtrans webhook (notification URL)
    public function webhookMidtrans(Request $request)
    {
        if (!config('payment.midtrans.enabled')) {
            return response()->json(['status' => 'gateway_disabled'], 400);
        }

        $serverKey   = config('payment.midtrans.server_key');
        $payload     = $request->all();
        $orderId     = $payload['order_id'] ?? null;
        $statusCode  = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;

        // Verify signature
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            return response()->json(['status' => 'invalid_signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';

        if (in_array($transactionStatus, ['capture', 'settlement']) && $fraudStatus === 'accept') {
            if (preg_match('/SUB-(\d+)/', $orderId, $m)) {
                $sub = UserSubscription::find($m[1]);
                if ($sub && $sub->status === 'pending_payment') {
                    $months = $sub->billing_cycle === 'yearly' ? 12 : 1;
                    $sub->update([
                        'status'         => 'active',
                        'starts_at'      => now(),
                        'ends_at'        => now()->addMonths($months),
                        'ai_quota_used'  => 0,
                        'quota_reset_at' => now()->addMonth(),
                    ]);
                    app(\App\Services\ReferralService::class)->convertSubscription($sub->fresh());
                    $this->notificationService->notifySubscriptionActivated($sub->fresh()->load('package', 'user'));
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // Xendit webhook (callback URL)
    public function webhookXendit(Request $request)
    {
        if (!config('payment.xendit.enabled')) {
            return response()->json(['status' => 'gateway_disabled'], 400);
        }

        // Verify webhook token
        $token = $request->header('x-callback-token');
        if ($token !== config('payment.xendit.webhook_token')) {
            return response()->json(['status' => 'invalid_token'], 403);
        }

        $payload = $request->all();
        $status  = $payload['status'] ?? '';
        $extId   = $payload['external_id'] ?? '';

        if ($status === 'PAID' || $status === 'SETTLED') {
            if (preg_match('/SUB-(\d+)/', $extId, $m)) {
                $sub = UserSubscription::find($m[1]);
                if ($sub && $sub->status === 'pending_payment') {
                    $months = $sub->billing_cycle === 'yearly' ? 12 : 1;
                    $sub->update([
                        'status'         => 'active',
                        'starts_at'      => now(),
                        'ends_at'        => now()->addMonths($months),
                        'ai_quota_used'  => 0,
                        'quota_reset_at' => now()->addMonth(),
                    ]);
                    app(\App\Services\ReferralService::class)->convertSubscription($sub->fresh());
                    $this->notificationService->notifySubscriptionActivated($sub->fresh()->load('package', 'user'));
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
