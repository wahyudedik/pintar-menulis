<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdPlacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'ad_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get active ad placements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get ad placement by location
     */
    public static function getByLocation($location)
    {
        return self::where('location', $location)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get ad code for location
     */
    public static function getAdCode($location)
    {
        $placement = self::getByLocation($location);
        return $placement ? $placement->ad_code : null;
    }
}
