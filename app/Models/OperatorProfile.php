<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'portfolio_url',
        'specializations',
        'portfolio_items',
        'base_price',
        'completed_orders',
        'average_rating',
        'total_reviews',
        'total_earnings',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_verified',
        'is_available',
        'verified_at',
    ];

    protected $casts = [
        'specializations' => 'array',
        'portfolio_items' => 'array',
        'base_price' => 'integer',
        'completed_orders' => 'integer',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'total_earnings' => 'integer',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'operator_id');
    }
}
