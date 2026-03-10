<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaptionQualityScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'caption',
        'platform',
        'industry',
        'overall_score',
        'engagement_score',
        'clarity_score',
        'cta_score',
        'emoji_score',
        'analysis_data',
    ];

    protected $casts = [
        'analysis_data' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
