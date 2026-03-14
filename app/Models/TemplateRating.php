<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'user_id',
        'rating',
        'review',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(UserTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Events
    protected static function booted()
    {
        static::created(function ($rating) {
            $rating->template->updateRating();
        });

        static::updated(function ($rating) {
            $rating->template->updateRating();
        });

        static::deleted(function ($rating) {
            $rating->template->updateRating();
        });
    }
}
