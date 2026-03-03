<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRevision extends Model
{
    protected $fillable = [
        'order_id',
        'revision_number',
        'result',
        'operator_notes',
        'revision_request',
        'submitted_at',
        'revision_requested_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'revision_requested_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
