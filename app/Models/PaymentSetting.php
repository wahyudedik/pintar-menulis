<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'gateway_type',
        'bank_name',
        'account_number',
        'account_name',
        'qr_code_path',
        'gateway_config',
        'is_active',
        'is_enabled',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_enabled'     => 'boolean',
        'gateway_config' => 'array',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeManual($query)
    {
        return $query->where('gateway_type', 'manual_transfer');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isManual(): bool
    {
        return $this->gateway_type === 'manual_transfer';
    }

    public function isMidtrans(): bool
    {
        return $this->gateway_type === 'midtrans';
    }

    public function isXendit(): bool
    {
        return $this->gateway_type === 'xendit';
    }

    public function getConfig(string $key, $default = null)
    {
        return data_get($this->gateway_config, $key, $default);
    }

    // ── Static helpers ───────────────────────────────────────────────────────

    public static function getActive(): self|null
    {
        // Priority: midtrans > xendit > manual
        return static::enabled()->active()
            ->orderByRaw("FIELD(gateway_type, 'midtrans', 'xendit', 'manual_transfer')")
            ->first();
    }

    public static function getMidtrans(): self|null
    {
        return static::where('gateway_type', 'midtrans')->first();
    }

    public static function getXendit(): self|null
    {
        return static::where('gateway_type', 'xendit')->first();
    }

    public static function getManualBanks()
    {
        return static::manual()->active()->get();
    }

    public static function getEnabledGateways(): array
    {
        $gateways = [];

        $midtrans = static::getMidtrans();
        if ($midtrans?->is_enabled || config('payment.midtrans.enabled')) $gateways[] = 'midtrans';

        $xendit = static::getXendit();
        if ($xendit?->is_enabled || config('payment.xendit.enabled')) $gateways[] = 'xendit';

        if (static::manual()->where('is_active', true)->exists()) {
            $gateways[] = 'manual_transfer';
        }

        return $gateways;
    }
}
