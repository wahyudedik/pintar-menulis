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
        'guru_total_earnings',
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
            'guru_total_earnings' => 'decimal:2',
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

    public function imageCaptions()
    {
        return $this->hasMany(ImageCaption::class);
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
}

