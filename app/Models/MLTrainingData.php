<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLTrainingData extends Model
{
    protected $table = 'ml_training_data';

    protected $fillable = [
        'guru_id',
        'copywriting_request_id',
        'input_prompt',
        'ai_output',
        'corrected_output',
        'feedback_notes',
        'quality_rating',
        'metadata',
        'earnings_paid',
    ];

    protected $casts = [
        'metadata' => 'array',
        'earnings_paid' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function copywritingRequest()
    {
        return $this->belongsTo(CopywritingRequest::class);
    }
}

