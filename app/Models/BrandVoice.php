<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandVoice extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'industry',
        'target_market',
        'tone',
        'platform',
        'keywords',
        'local_language',
        'brand_description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the brand voice
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set this brand voice as default and unset others
     */
    public function setAsDefault(): void
    {
        // Unset all other defaults for this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }
}
