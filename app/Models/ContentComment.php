<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentComment extends Model
{
    protected $fillable = [
        'content_id',
        'user_id',
        'comment',
        'type',
        'highlighted_text',
        'parent_id',
        'is_resolved'
    ];

    protected $casts = [
        'highlighted_text' => 'array',
        'is_resolved' => 'boolean',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(ProjectContent::class, 'content_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ContentComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ContentComment::class, 'parent_id');
    }

    public function isComment(): bool
    {
        return $this->type === 'comment';
    }

    public function isSuggestion(): bool
    {
        return $this->type === 'suggestion';
    }

    public function isApproval(): bool
    {
        return $this->type === 'approval';
    }

    public function isRejection(): bool
    {
        return $this->type === 'rejection';
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'comment' => 'Comment',
            'suggestion' => 'Suggestion',
            'approval' => 'Approval',
            'rejection' => 'Rejection',
            default => ucfirst($this->type)
        };
    }

    public function getTypeColor(): string
    {
        return match($this->type) {
            'comment' => 'bg-blue-100 text-blue-800',
            'suggestion' => 'bg-yellow-100 text-yellow-800',
            'approval' => 'bg-green-100 text-green-800',
            'rejection' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function resolve(): void
    {
        $this->update(['is_resolved' => true]);
    }

    public function unresolve(): void
    {
        $this->update(['is_resolved' => false]);
    }
}