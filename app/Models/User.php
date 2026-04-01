<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'bio',
        'phone',
        'location',
        'website',
        'provider',
        'whatsapp_number',
        'whatsapp_verified',
        'whatsapp_verified_at',
        'whatsapp_verification_code',
        'whatsapp_preferences',
        'whatsapp_notifications_enabled',
        'last_whatsapp_interaction',
        'guru_total_earnings',
        'referral_code',
        'referred_by_id',
        'referral_earnings',
        'onboarding_completed',
        'business_type',
        'business_name',
        'primary_platform',
        'content_goal',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'whatsapp_verified' => 'boolean',
            'whatsapp_verified_at' => 'datetime',
            'whatsapp_preferences' => 'array',
            'whatsapp_notifications_enabled' => 'boolean',
            'last_whatsapp_interaction' => 'datetime',
            'guru_total_earnings' => 'decimal:2',
            'referral_earnings'   => 'decimal:2',
            'onboarding_completed' => 'boolean',
        ];
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function copywritingRequests()
    {
        return $this->hasMany(CopywritingRequest::class);
    }

    public function assignedCopywritingRequests()
    {
        return $this->hasMany(CopywritingRequest::class, 'assigned_to');
    }

    public function operatorProfile()
    {
        return $this->hasOne(OperatorProfile::class);
    }

    public function operatorOrders()
    {
        return $this->hasMany(Order::class, 'operator_id');
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function mlTrainingData()
    {
        return $this->hasMany(MLTrainingData::class, 'guru_id');
    }

    public function referrals()
    {
        return $this->hasMany(\App\Models\Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function brandVoices()
    {
        return $this->hasMany(BrandVoice::class);
    }


    public function defaultBrandVoice()
    {
        return $this->hasOne(BrandVoice::class)->where('is_default', true);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function contentCalendars()
    {
        return $this->hasMany(ContentCalendar::class);
    }

    public function imageCaptions()
    {
        return $this->hasMany(ImageCaption::class);
    }

    public function templates()
    {
        return $this->hasMany(UserTemplate::class);
    }

    public function templateRatings()
    {
        return $this->hasMany(TemplateRating::class);
    }

    public function templateFavorites()
    {
        return $this->hasMany(TemplateFavorite::class);
    }

    public function templatePurchases()
    {
        return $this->hasMany(TemplatePurchase::class, 'buyer_id');
    }

    public function templateSales()
    {
        return $this->hasMany(TemplatePurchase::class, 'seller_id');
    }

    // ── Subscription ─────────────────────────────────────────────────────────

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->with('package')
            ->whereIn('status', ['active', 'trial'])
            ->latest();
    }

    public function hasActiveSubscription(): bool
    {
        $sub = $this->activeSubscription()->first();
        return $sub && $sub->isValid();
    }

    public function hasUsedTrial(): bool
    {
        return $this->subscriptions()->whereIn('status', ['trial', 'active', 'expired', 'cancelled'])
            ->where('trial_used', true)
            ->whereNotNull('trial_starts_at') // only real trials have this set
            ->exists();
    }

    public function currentSubscription(): ?UserSubscription
    {
        return $this->activeSubscription()->first();
    }

    public function canUseAI(): bool
    {
        $sub = $this->currentSubscription();
        return $sub && $sub->isValid() && $sub->remaining_quota > 0;
    }

    /**
     * 📱 WhatsApp Integration Methods
     */
    
    /**
     * Get WhatsApp messages for this user
     */
    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * Get WhatsApp subscription for this user
     */
    public function whatsappSubscription()
    {
        return $this->hasOne(WhatsAppSubscription::class);
    }

    /**
     * Check if user has verified WhatsApp number
     */
    public function hasVerifiedWhatsApp(): bool
    {
        return $this->whatsapp_verified && !empty($this->whatsapp_number);
    }

    /**
     * Get formatted WhatsApp number
     */
    public function getFormattedWhatsAppAttribute(): ?string
    {
        if (!$this->whatsapp_number) {
            return null;
        }

        $phone = $this->whatsapp_number;
        
        if (str_starts_with($phone, '62')) {
            return '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * Generate WhatsApp verification code
     */
    public function generateWhatsAppVerificationCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'whatsapp_verification_code' => $code
        ]);

        return $code;
    }

    /**
     * Verify WhatsApp number with code
     */
    public function verifyWhatsApp(string $code): bool
    {
        if ($this->whatsapp_verification_code === $code) {
            $this->update([
                'whatsapp_verified' => true,
                'whatsapp_verified_at' => now(),
                'whatsapp_verification_code' => null
            ]);

            return true;
        }

        return false;
    }

    /**
     * Link WhatsApp number to user account
     */
    public function linkWhatsApp(string $phoneNumber): void
    {
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        
        $this->update([
            'whatsapp_number' => $formattedNumber,
            'whatsapp_verified' => false,
            'whatsapp_verified_at' => null,
            'whatsapp_notifications_enabled' => true
        ]);

        // Create or update subscription
        WhatsAppSubscription::updateOrCreate(
            ['phone_number' => $formattedNumber],
            [
                'user_id' => $this->id,
                'is_active' => true,
                'subscribed_at' => now(),
                'last_interaction_at' => now()
            ]
        );
    }

    /**
     * Update WhatsApp interaction timestamp
     */
    public function updateWhatsAppInteraction(): void
    {
        $this->update(['last_whatsapp_interaction' => now()]);
    }

    /**
     * Get WhatsApp preferences with defaults
     */
    public function getWhatsAppPreferences(): array
    {
        $defaults = [
            'daily_content' => true,
            'weekly_reminder' => true,
            'trending_notifications' => false,
            'promotional_messages' => false,
            'business_type' => null,
            'target_audience' => 'Gen Z Indonesia',
            'content_types' => ['caption', 'story'],
            'platforms' => ['instagram', 'tiktok'],
            'preferred_time' => '08:00',
            'timezone' => 'Asia/Jakarta',
            'language' => 'bahasa_indonesia',
            'tone_preference' => 'engaging'
        ];

        return array_merge($defaults, $this->whatsapp_preferences ?? []);
    }

    /**
     * Update WhatsApp preferences
     */
    public function updateWhatsAppPreferences(array $preferences): void
    {
        $currentPrefs = $this->getWhatsAppPreferences();
        $newPrefs = array_merge($currentPrefs, $preferences);
        
        $this->update(['whatsapp_preferences' => $newPrefs]);
        
        // Update subscription if exists
        if ($this->whatsappSubscription) {
            $this->whatsappSubscription->updatePreferences($preferences);
        }
    }

    /**
     * Check if user should receive WhatsApp notifications
     */
    public function shouldReceiveWhatsAppNotifications(): bool
    {
        return $this->hasVerifiedWhatsApp() && 
               $this->whatsapp_notifications_enabled &&
               $this->whatsappSubscription?->is_active;
    }

    /**
     * Get WhatsApp activity summary
     */
    public function getWhatsAppActivitySummary(): array
    {
        $messages = $this->whatsappMessages();
        
        return [
            'total_messages' => $messages->count(),
            'messages_sent' => $messages->outgoing()->count(),
            'messages_received' => $messages->incoming()->count(),
            'last_message' => $messages->latest()->first()?->created_at,
            'first_message' => $messages->oldest()->first()?->created_at,
            'is_subscribed' => $this->whatsappSubscription?->is_active ?? false,
            'subscription_date' => $this->whatsappSubscription?->subscribed_at,
            'preferred_language' => $this->getWhatsAppPreferences()['language']
        ];
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle Indonesian phone numbers
        if (substr($phone, 0, 1) === '0') {
            // Replace leading 0 with 62
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            // Add 62 if not present
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Scope for users with verified WhatsApp
     */
    public function scopeWhatsAppVerified($query)
    {
        return $query->where('whatsapp_verified', true)
                    ->whereNotNull('whatsapp_number');
    }

    /**
     * Scope for users with WhatsApp notifications enabled
     */
    public function scopeWhatsAppNotificationsEnabled($query)
    {
        return $query->where('whatsapp_notifications_enabled', true)
                    ->whatsAppVerified();
    }

}
