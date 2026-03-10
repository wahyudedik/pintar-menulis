<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignAnalytic extends Model
{
    use HasFactory;

    protected $table = 'campaign_analytics';

    protected $fillable = [
        'user_id',
        'campaign_name',
        'total_captions',
        'average_rating',
        'top_patterns',
        'weak_areas',
        'recommendations',
        'trending_elements',
        'full_analysis',
    ];

    protected $casts = [
        'top_patterns' => 'json',
        'weak_areas' => 'json',
        'recommendations' => 'json',
        'trending_elements' => 'json',
        'full_analysis' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
