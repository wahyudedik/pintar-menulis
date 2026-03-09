<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerInformation extends Model
{
    protected $table = 'banner_information';
    
    protected $fillable = [
        'type',
        'title',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active banner by type
     */
    public static function getActiveByType(string $type): ?self
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->whereNotNull('title')
            ->whereNotNull('content')
            ->first();
    }
}
