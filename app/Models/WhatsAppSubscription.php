<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'user_id',
        'daily_content',
        'weekly_reminder',
        'trending_notifications',
        'promotional_messages',
        'business_type',
        'target_audience',
        'content_types',
        'platforms',
        'preferred_time',
        'timezone',
        'preferred_days',
        'language',
        'tone_preference',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'last_interaction_at'
    ];

    protected $casts = [
        'daily_content' => 'boolean',
        'weekly_reminder' => 'boolean',
        'trending_notifications' => 'boolean',
        'promotional_messages' => 'boolean',
        'content_types' => 'array',
        'platforms' => 'array',
        'preferred_days' => 'array',
        'is_active' => 'boolean',
        'preferred_time' => 'datetime:H:i',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_interaction_at' => 'datetime'
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get WhatsApp messages for this subscription
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'phone_number', 'phone_number');
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for daily content subscribers
     */
    public function scopeDailyContent($query)
    {
        return $query->where('daily_content', true)->active();
    }

    /**
     * Scope for trending notifications subscribers
     */
    public function scopeTrendingNotifications($query)
    {
        return $query->where('trending_notifications', true)->active();
    }

    /**
     * Scope for specific business type
     */
    public function scopeBusinessType($query, string $businessType)
    {
        return $query->where('business_type', $businessType);
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone_number;
        
        if (str_starts_with($phone, '62')) {
            return '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * Check if user should receive content at current time
     */
    public function shouldReceiveContentNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now($this->timezone);
        $preferredTime = $now->copy()->setTimeFromTimeString($this->preferred_time);
        
        // Check if current time is within 1 hour of preferred time
        $timeDiff = abs($now->diffInMinutes($preferredTime));
        
        // Check if today is a preferred day
        $preferredDays = $this->preferred_days ?? [1, 2, 3, 4, 5, 6, 7]; // Default all days
        $todayDayOfWeek = $now->dayOfWeek === 0 ? 7 : $now->dayOfWeek; // Convert Sunday from 0 to 7
        
        return $timeDiff <= 60 && in_array($todayDayOfWeek, $preferredDays);
    }

    /**
     * Update last interaction timestamp
     */
    public function updateLastInteraction(): void
    {
        $this->update(['last_interaction_at' => now()]);
    }

    /**
     * Subscribe user with preferences
     */
    public static function subscribe(string $phoneNumber, array $preferences = []): self
    {
        return static::updateOrCreate(
            ['phone_number' => $phoneNumber],
            array_merge([
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'last_interaction_at' => now()
            ], $preferences)
        );
    }

    /**
     * Unsubscribe user
     */
    public function unsubscribe(): void
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now()
        ]);
    }

    /**
     * Get subscription preferences as array
     */
    public function getPreferences(): array
    {
        return [
            'daily_content' => $this->daily_content,
            'weekly_reminder' => $this->weekly_reminder,
            'trending_notifications' => $this->trending_notifications,
            'promotional_messages' => $this->promotional_messages,
            'business_type' => $this->business_type,
            'target_audience' => $this->target_audience,
            'content_types' => $this->content_types ?? ['caption', 'story'],
            'platforms' => $this->platforms ?? ['instagram', 'tiktok'],
            'preferred_time' => $this->preferred_time->format('H:i'),
            'timezone' => $this->timezone,
            'preferred_days' => $this->preferred_days ?? [1, 2, 3, 4, 5],
            'language' => $this->language,
            'tone_preference' => $this->tone_preference
        ];
    }

    /**
     * Update preferences
     */
    public function updatePreferences(array $preferences): void
    {
        $this->update($preferences);
        $this->updateLastInteraction();
    }

    /**
     * Get subscribers for daily content
     */
    public static function getDailyContentSubscribers(): \Illuminate\Database\Eloquent\Collection
    {
        return static::dailyContent()
            ->where('last_interaction_at', '>=', now()->subDays(30)) // Active in last 30 days
            ->get();
    }

    /**
     * Get subscribers for trending notifications
     */
    public static function getTrendingSubscribers(): \Illuminate\Database\Eloquent\Collection
    {
        return static::trendingNotifications()
            ->where('last_interaction_at', '>=', now()->subDays(14)) // Active in last 14 days
            ->get();
    }

    /**
     * Get subscription statistics
     */
    public static function getStats(): array
    {
        return [
            'total_subscriptions' => static::count(),
            'active_subscriptions' => static::active()->count(),
            'daily_content_subscribers' => static::dailyContent()->count(),
            'trending_subscribers' => static::trendingNotifications()->count(),
            'business_types' => static::active()
                ->whereNotNull('business_type')
                ->groupBy('business_type')
                ->selectRaw('business_type, count(*) as count')
                ->pluck('count', 'business_type')
                ->toArray(),
            'languages' => static::active()
                ->groupBy('language')
                ->selectRaw('language, count(*) as count')
                ->pluck('count', 'language')
                ->toArray()
        ];
    }
}
