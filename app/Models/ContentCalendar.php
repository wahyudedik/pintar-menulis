<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentCalendar extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'duration',
        'start_date',
        'end_date',
        'category',
        'platform',
        'tone',
        'brief',
        'content_items',
        'status',
    ];

    protected $casts = [
        'content_items' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get content for specific date
     */
    public function getContentForDate($date)
    {
        $dateStr = is_string($date) ? $date : $date->format('Y-m-d');
        
        foreach ($this->content_items as $item) {
            if ($item['scheduled_date'] === $dateStr) {
                return $item;
            }
        }
        
        return null;
    }

    /**
     * Get all scheduled dates
     */
    public function getScheduledDates()
    {
        return collect($this->content_items)->pluck('scheduled_date')->toArray();
    }
}
