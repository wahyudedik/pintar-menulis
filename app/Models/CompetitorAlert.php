<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'competitor_id',
        'competitor_post_id',
        'alert_type',
        'alert_title',
        'alert_message',
        'alert_data',
        'is_read',
        'triggered_at',
    ];

    protected $casts = [
        'alert_data' => 'array',
        'is_read' => 'boolean',
        'triggered_at' => 'datetime',
    ];

    /**
     * Get the user that owns the alert
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the competitor
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get the post (if applicable)
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(CompetitorPost::class, 'competitor_post_id');
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Get message attribute (alias for alert_message)
     */
    public function getMessageAttribute(): string
    {
        return $this->alert_message;
    }

    /**
     * Get alert type label
     */
    public function getAlertTypeLabelAttribute(): string
    {
        $labels = [
            'new_post' => 'Post Baru',
            'promo_detected' => 'Promo Terdeteksi',
            'viral_content' => 'Konten Viral',
            'pattern_change' => 'Perubahan Pola',
            'engagement_spike' => 'Lonjakan Engagement',
        ];

        return $labels[$this->alert_type] ?? $this->alert_type;
    }

    /**
     * Get alert type icon
     */
    public function getAlertTypeIconAttribute(): string
    {
        $icons = [
            'new_post' => '📝',
            'promo_detected' => '🏷️',
            'viral_content' => '🔥',
            'pattern_change' => '📊',
            'engagement_spike' => '📈',
        ];

        return $icons[$this->alert_type] ?? '🔔';
    }

    /**
     * Get alert type color
     */
    public function getAlertTypeColorAttribute(): string
    {
        $colors = [
            'new_post' => 'blue',
            'promo_detected' => 'orange',
            'viral_content' => 'red',
            'pattern_change' => 'purple',
            'engagement_spike' => 'green',
        ];

        return $colors[$this->alert_type] ?? 'gray';
    }

    /**
     * Scope for unread alerts
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for recent alerts (last 7 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('triggered_at', '>=', now()->subDays(7));
    }
}
