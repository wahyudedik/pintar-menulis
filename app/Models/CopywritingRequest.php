<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CopywritingRequest extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'assigned_to',
        'type',
        'platform',
        'brief',
        'product_images',
        'tone',
        'keywords',
        'ai_generated_content',
        'final_content',
        'status',
        'revision_count',
        'revision_notes',
        'rating',
        'feedback',
        'deadline',
        'completed_at',
    ];

    protected $casts = [
        'product_images' => 'array',
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'rating' => 'decimal:1',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
