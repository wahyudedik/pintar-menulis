<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'caption_quota',
        'product_description_quota',
        'revision_limit',
        'response_time_hours',
        'consultation_included',
        'content_calendar_included',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'consultation_included' => 'boolean',
        'content_calendar_included' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
