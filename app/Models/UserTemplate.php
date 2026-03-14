<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'platform',
        'tone',
        'template_content',
        'format_instructions',
        'tags',
        'is_public',
        'is_approved',
        'status',
        'rejection_reason',
        'is_premium',
        'price',
        'license_type',
        'usage_count',
        'download_count',
        'favorite_count',
        'rating_average',
        'total_ratings',
        'total_sales',
        'total_revenue',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_approved' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'total_revenue' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(TemplateRating::class, 'template_id');
    }

    public function favorites()
    {
        return $this->hasMany(TemplateFavorite::class, 'template_id');
    }

    public function purchases()
    {
        return $this->hasMany(TemplatePurchase::class, 'template_id');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true)->where('is_approved', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    // Helper Methods
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function incrementDownload()
    {
        $this->increment('download_count');
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function isPurchasedBy($userId)
    {
        return $this->purchases()->where('buyer_id', $userId)->where('payment_status', 'completed')->exists();
    }

    public function canBeAccessedBy($userId)
    {
        // Owner can always access
        if ($this->user_id == $userId) {
            return true;
        }

        // Free templates can be accessed by anyone
        if (!$this->is_premium) {
            return true;
        }

        // Premium templates require purchase
        return $this->isPurchasedBy($userId);
    }

    public function updateRating()
    {
        $this->rating_average = $this->ratings()->avg('rating') ?? 0;
        $this->total_ratings = $this->ratings()->count();
        $this->save();
    }
}
