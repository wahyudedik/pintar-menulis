<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImageAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'image_description',
        'suggested_captions',
        'hashtags',
        'best_time_to_post',
        'content_type',
        'full_analysis',
    ];

    protected $casts = [
        'suggested_captions' => 'json',
        'hashtags' => 'json',
        'full_analysis' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
