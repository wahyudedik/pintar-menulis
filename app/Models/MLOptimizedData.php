<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLOptimizedData extends Model
{
    protected $table = 'ml_optimized_data';
    
    protected $fillable = [
        'type',
        'industry',
        'platform',
        'data',
        'performance_score',
        'usage_count',
        'is_active',
        'last_trained_at',
        'metadata',
    ];
    
    protected $casts = [
        'performance_score' => 'decimal:2',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
        'last_trained_at' => 'datetime',
        'metadata' => 'array',
    ];
    
    /**
     * Scope for active data
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope for specific industry
     */
    public function scopeForIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }
    
    /**
     * Scope for high performing data
     */
    public function scopeHighPerforming($query, float $minScore = 5.0)
    {
        return $query->where('performance_score', '>=', $minScore);
    }
}
