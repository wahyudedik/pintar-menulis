<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'subscription_id',
        'status',
        'type',
        'subscription_commission_paid',
        'commission_amount',
        'converted_at',
    ];

    protected $casts = [
        'commission_amount'            => 'decimal:2',
        'converted_at'                 => 'datetime',
        'subscription_commission_paid' => 'boolean',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class);
    }
}
