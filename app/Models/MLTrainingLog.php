<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLTrainingLog extends Model
{
    protected $table = 'ml_training_logs';
    
    protected $fillable = [
        'trained_at',
        'duration_seconds',
        'hashtags_trained',
        'keywords_trained',
        'topics_trained',
        'hooks_trained',
        'ctas_trained',
        'total_trained',
        'errors',
        'status',
    ];
    
    protected $casts = [
        'trained_at' => 'datetime',
        'duration_seconds' => 'integer',
        'hashtags_trained' => 'integer',
        'keywords_trained' => 'integer',
        'topics_trained' => 'integer',
        'hooks_trained' => 'integer',
        'ctas_trained' => 'integer',
        'total_trained' => 'integer',
        'errors' => 'array',
    ];
    
    /**
     * Scope for successful trainings
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }
    
    /**
     * Scope for recent trainings
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('trained_at', '>=', now()->subDays($days));
    }
}
