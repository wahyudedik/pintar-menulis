<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(UserTemplate::class, 'template_id');
    }

    // Events
    protected static function booted()
    {
        static::created(function ($favorite) {
            $favorite->template->increment('favorite_count');
        });

        static::deleted(function ($favorite) {
            $favorite->template->decrement('favorite_count');
        });
    }
}
