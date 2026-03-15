<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Str;

class ReferralService
{
    // Komisi saat referred user berhasil register
    const COMMISSION_SIGNUP = 500;

    // Komisi saat referred user pertama kali berlangganan berbayar (sekali seumur hidup)
    const COMMISSION_SUBSCRIPTION = 1000;

    /**
     * Generate a unique referral code for a user.
     */
    public function generateCode(User $user): string
    {
        if ($user->referral_code) {
            return $user->referral_code;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);

        return $code;
    }

    /**
     * Track signup via referral code + langsung beri komisi signup Rp 500.
     * Dipanggil saat registrasi.
     */
    public function trackSignup(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return;
        }

        $newUser->update(['referred_by_id' => $referrer->id]);

        // Catat referral + langsung beri komisi signup
        Referral::create([
            'referrer_id'       => $referrer->id,
            'referred_user_id'  => $newUser->id,
            'type'              => 'signup',
            'status'            => 'converted',
            'commission_amount' => self::COMMISSION_SIGNUP,
            'converted_at'      => now(),
        ]);

        // Langsung kredit ke referrer
        $referrer->increment('referral_earnings', self::COMMISSION_SIGNUP);
    }

    /**
     * Beri komisi subscription Rp 1.000 saat referred user pertama kali berlangganan berbayar.
     * Hanya sekali per referred user — dipanggil saat admin verifikasi subscription.
     */
    public function convertSubscription(UserSubscription $subscription): void
    {
        $user = $subscription->user;

        if (!$user->referred_by_id) {
            return;
        }

        // Cek apakah komisi subscription sudah pernah diberikan untuk user ini
        $alreadyPaid = Referral::where('referred_user_id', $user->id)
            ->where('type', 'subscription')
            ->where('subscription_commission_paid', true)
            ->exists();

        if ($alreadyPaid) {
            return; // Sudah dapat komisi subscription sebelumnya, skip
        }

        Referral::create([
            'referrer_id'                  => $user->referred_by_id,
            'referred_user_id'             => $user->id,
            'subscription_id'              => $subscription->id,
            'type'                         => 'subscription',
            'status'                       => 'converted',
            'commission_amount'            => self::COMMISSION_SUBSCRIPTION,
            'subscription_commission_paid' => true,
            'converted_at'                 => now(),
        ]);

        // Kredit ke referrer
        User::find($user->referred_by_id)->increment('referral_earnings', self::COMMISSION_SUBSCRIPTION);
    }

    /**
     * Get referral stats for a user.
     */
    public function getStats(User $user): array
    {
        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referredUser')
            ->orderByDesc('created_at')
            ->get();

        $pendingWithdrawals = \App\Models\WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        return [
            'referral_code'           => $user->referral_code ?? $this->generateCode($user),
            'total_referrals'         => $referrals->where('type', 'signup')->count(),
            'signup_commissions'      => $referrals->where('type', 'signup')->count(),
            'subscription_commissions'=> $referrals->where('type', 'subscription')->count(),
            'total_earnings'          => $user->referral_earnings,
            'pending_withdrawal'      => $pendingWithdrawals,
            'available_balance'       => $user->referral_earnings - $pendingWithdrawals,
            'referrals'               => $referrals,
        ];
    }
}
