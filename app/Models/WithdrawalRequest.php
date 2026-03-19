<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'notes',
        'admin_notes',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
