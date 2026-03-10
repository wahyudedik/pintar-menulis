<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageCaption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'detected_objects',
        'caption_single',
        'caption_carousel',
        'editing_tips',
        'dominant_colors',
    ];

    protected $casts = [
        'detected_objects' => 'array',
        'caption_carousel' => 'array',
        'editing_tips' => 'array',
        'dominant_colors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
