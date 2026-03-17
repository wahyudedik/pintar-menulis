<?php

namespace App\Models;

use App\Enums\PackageFeatures;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'yearly_price',
        'ai_quota_monthly', 'caption_quota', 'product_description_quota',
        'revision_limit', 'response_time_hours',
        'consultation_included', 'content_calendar_included',
        'has_trial', 'trial_days', 'is_featured', 'is_active',
        'badge_text', 'badge_color', 'features', 'allowed_features', 'sort_order',
    ];

    protected $casts = [
        'price'                      => 'integer',
        'yearly_price'               => 'integer',
        'consultation_included'      => 'boolean',
        'content_calendar_included'  => 'boolean',
        'has_trial'                  => 'boolean',
        'is_featured'                => 'boolean',
        'is_active'                  => 'boolean',
        'features'                   => 'array',
        'allowed_features'           => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function getYearlySavingsAttribute(): int
    {
        if (!$this->yearly_price || !$this->price) return 0;
        return ($this->price * 12) - $this->yearly_price;
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Resolve the tier name based on price for fallback defaults.
     */
    public function getTier(): string
    {
        if ($this->price == 0)       return 'free';
        if ($this->price <= 100000)  return 'starter';
        if ($this->price <= 200000)  return 'pro';
        return 'business';
    }

    /**
     * Get the effective allowed features:
     * - Uses allowed_features from DB if set
     * - Falls back to tier defaults
     */
    public function getAllowedFeaturesList(): array
    {
        if (!empty($this->allowed_features)) {
            return $this->allowed_features;
        }
        $defaults = PackageFeatures::tierDefaults();
        return $defaults[$this->getTier()] ?? [];
    }

    /**
     * Check if this package allows a specific feature key.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->getAllowedFeaturesList());
    }
}
