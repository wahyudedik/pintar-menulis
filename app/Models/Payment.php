<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'escrow_status',
        'payment_details',
        'paid_at',
        'expired_at',
        'proof_image',
        'verified_at',
        'verified_by',
        'released_at',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'verified_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
