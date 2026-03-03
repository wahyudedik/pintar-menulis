<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLModelVersion extends Model
{
    protected $table = 'ml_model_versions';

    protected $fillable = [
        'version_name',
        'version',
        'description',
        'training_data_count',
        'accuracy_score',
        'is_active',
    ];

    protected $casts = [
        'training_data_count' => 'integer',
        'accuracy_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}

