<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'yearly_price',
        'ai_quota_monthly', 'caption_quota', 'product_description_quota',
        'revision_limit', 'response_time_hours',
        'consultation_included', 'content_calendar_included',
        'has_trial', 'trial_days', 'is_featured', 'is_active',
        'badge_text', 'badge_color', 'features', 'sort_order',
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
}
