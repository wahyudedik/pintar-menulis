<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'direction',
        'message_type',
        'message_content',
        'media_url',
        'media_path',
        'metadata',
        'status',
        'external_id',
        'user_id',
        'caption_history_id',
        'processed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the caption history associated with this message
     */
    public function captionHistory(): BelongsTo
    {
        return $this->belongsTo(CaptionHistory::class);
    }

    /**
     * Scope for incoming messages
     */
    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    /**
     * Scope for outgoing messages
     */
    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    /**
     * Scope for specific phone number
     */
    public function scopeForPhone($query, string $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }

    /**
     * Scope for processed messages
     */
    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Mark message as processed
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'processed_at' => now(),
            'status' => 'delivered'
        ]);
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
     * Check if message has media
     */
    public function hasMedia(): bool
    {
        return !empty($this->media_url) || !empty($this->media_path);
    }

    /**
     * Get conversation history for a phone number
     */
    public static function getConversation(string $phoneNumber, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::forPhone($phoneNumber)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Record incoming WhatsApp message
     */
    public static function recordIncoming(array $data): self
    {
        return static::create([
            'phone_number' => $data['phone_number'],
            'direction' => 'incoming',
            'message_type' => $data['message_type'] ?? 'text',
            'message_content' => $data['message_content'] ?? null,
            'media_url' => $data['media_url'] ?? null,
            'external_id' => $data['external_id'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'status' => 'delivered'
        ]);
    }

    /**
     * Record outgoing WhatsApp message
     */
    public static function recordOutgoing(array $data): self
    {
        return static::create([
            'phone_number' => $data['phone_number'],
            'direction' => 'outgoing',
            'message_type' => $data['message_type'] ?? 'text',
            'message_content' => $data['message_content'] ?? null,
            'media_url' => $data['media_url'] ?? null,
            'media_path' => $data['media_path'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'caption_history_id' => $data['caption_history_id'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'status' => $data['status'] ?? 'pending'
        ]);
    }
}
