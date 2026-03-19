<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id', 'package_id', 'status', 'billing_cycle',
        'trial_starts_at', 'trial_ends_at', 'starts_at', 'ends_at',
        'cancelled_at', 'trial_used', 'ai_quota_used', 'ai_quota_limit',
        'quota_reset_at', 'payment_method', 'payment_reference',
    ];

    protected $casts = [
        'trial_starts_at' => 'datetime',
        'trial_ends_at'   => 'datetime',
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
        'cancelled_at'    => 'datetime',
        'quota_reset_at'  => 'datetime',
        'trial_used'      => 'boolean',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function package()   { return $this->belongsTo(Package::class); }

    // ── Status helpers ──────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at?->isFuture();
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at?->isFuture();
    }

    public function isValid(): bool
    {
        return $this->isActive() || $this->isOnTrial();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->status === 'active' && $this->ends_at?->isPast())
            || ($this->status === 'trial' && $this->trial_ends_at?->isPast());
    }

    // ── Trial ────────────────────────────────────────────────────────────────

    public function getDaysRemainingAttribute(): int
    {
        if ($this->isOnTrial()) {
            return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
        }
        if ($this->isActive()) {
            return max(0, (int) now()->diffInDays($this->ends_at, false));
        }
        return 0;
    }

    public function getTrialProgressAttribute(): int
    {
        if (!$this->isOnTrial()) return 100;
        $total = $this->trial_starts_at->diffInDays($this->trial_ends_at);
        $used  = $this->trial_starts_at->diffInDays(now());
        return $total > 0 ? min(100, (int) (($used / $total) * 100)) : 100;
    }

    // ── Quota ────────────────────────────────────────────────────────────────

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->ai_quota_limit - $this->ai_quota_used);
    }

    public function getQuotaPercentageAttribute(): int
    {
        if ($this->ai_quota_limit <= 0) return 0;
        return min(100, (int) (($this->ai_quota_used / $this->ai_quota_limit) * 100));
    }

    public function consumeQuota(int $amount = 1): bool
    {
        if ($this->remaining_quota < $amount) return false;
        $this->increment('ai_quota_used', $amount);
        return true;
    }

    public function resetQuota(): void
    {
        $this->update([
            'ai_quota_used'  => 0,
            'quota_reset_at' => now()->addMonth(),
        ]);
    }

    // ── Factory methods ──────────────────────────────────────────────────────

    public static function startTrial(User $user, Package $package): self
    {
        // Defense-in-depth: cek di level model juga
        if ($user->hasUsedTrial()) {
            throw new \RuntimeException('User sudah pernah menggunakan trial.');
        }

        if (!$package->has_trial || ($package->trial_days ?? 0) <= 0) {
            throw new \RuntimeException('Paket ini tidak memiliki masa trial.');
        }

        if ($package->price == 0) {
            throw new \RuntimeException('Paket gratis tidak menggunakan mekanisme trial berbayar.');
        }

        // Cancel any existing trial (jangan cancel yang active/paid)
        self::where('user_id', $user->id)
            ->where('status', 'trial')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return self::create([
            'user_id'         => $user->id,
            'package_id'      => $package->id,
            'status'          => 'trial',
            'billing_cycle'   => 'monthly',
            'trial_starts_at' => now(),
            'trial_ends_at'   => now()->addDays($package->trial_days),
            'trial_used'      => true,
            'ai_quota_limit'  => $package->ai_quota_monthly,
            'ai_quota_used'   => 0,
            'quota_reset_at'  => now()->addMonth(),
        ]);
    }

    public static function activate(User $user, Package $package, string $billingCycle = 'monthly', string $paymentRef = null): self
    {
        $months = $billingCycle === 'yearly' ? 12 : 1;
        $price  = $billingCycle === 'yearly' ? ($package->yearly_price ?? $package->price * 10) : $package->price;

        // Cancel existing
        self::where('user_id', $user->id)
            ->whereIn('status', ['trial', 'active', 'pending_payment'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return self::create([
            'user_id'           => $user->id,
            'package_id'        => $package->id,
            'status'            => 'active',
            'billing_cycle'     => $billingCycle,
            'starts_at'         => now(),
            'ends_at'           => now()->addMonths($months),
            'trial_used'        => true,
            'ai_quota_limit'    => $package->ai_quota_monthly,
            'ai_quota_used'     => 0,
            'quota_reset_at'    => now()->addMonth(),
            'payment_reference' => $paymentRef,
        ]);
    }
}
