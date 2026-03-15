<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLModelVersion extends Model
{
    protected $table = 'ml_model_versions';

    protected $fillable = [
        'version_name',
        'description',
        'training_count',
        'accuracy_score',
        'is_active',
        'trained_at',
    ];

    protected $casts = [
        'training_count' => 'integer',
        'accuracy_score' => 'decimal:2',
        'is_active' => 'boolean',
        'trained_at' => 'datetime',
    ];
}

