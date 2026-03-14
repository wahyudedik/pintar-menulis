<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'template_id',
        'seller_id',
        'price_paid',
        'license_type',
        'transaction_id',
        'payment_status',
        'purchased_at',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'purchased_at' => 'datetime',
    ];

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function template()
    {
        return $this->belongsTo(UserTemplate::class, 'template_id');
    }

    // Events
    protected static function booted()
    {
        static::created(function ($purchase) {
            if ($purchase->payment_status === 'completed') {
                $purchase->template->increment('total_sales');
                $purchase->template->increment('total_revenue', $purchase->price_paid);
            }
        });

        static::updated(function ($purchase) {
            if ($purchase->isDirty('payment_status') && $purchase->payment_status === 'completed') {
                $purchase->template->increment('total_sales');
                $purchase->template->increment('total_revenue', $purchase->price_paid);
            }
        });
    }
}
