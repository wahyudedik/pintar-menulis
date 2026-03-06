<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';
    
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'page_url',
        'browser',
        'screenshot',
        'priority',
        'status',
        'admin_response',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the user that submitted the feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get badge color based on type
     */
    public function getTypeBadgeColor(): string
    {
        return match($this->type) {
            'bug' => 'red',
            'feature' => 'blue',
            'improvement' => 'green',
            'question' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get badge color based on status
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'open' => 'yellow',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get badge color based on priority
     */
    public function getPriorityBadgeColor(): string
    {
        return match($this->priority) {
            'low' => 'gray',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'bug' => 'Bug Report',
            'feature' => 'Feature Request',
            'improvement' => 'Improvement',
            'question' => 'Question',
            default => $this->type,
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'open' => 'Terbuka',
            'in_progress' => 'Sedang Dikerjakan',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
            default => $this->status,
        };
    }
}
