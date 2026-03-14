<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectContent extends Model
{
    protected $table = 'project_content';

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'content',
        'platform',
        'content_type',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'metadata',
        'version',
        'is_current_version'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'metadata' => 'array',
        'is_current_version' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContentVersion::class, 'content_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ContentComment::class, 'content_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isInReview(): bool
    {
        return $this->status === 'review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function canEdit(User $user): bool
    {
        // Creator can always edit drafts
        if ($this->created_by === $user->id && $this->isDraft()) {
            return true;
        }

        // Check project member permissions
        $member = $this->project->members()->where('user_id', $user->id)->first();
        return $member && $member->canEdit();
    }

    public function canApprove(User $user): bool
    {
        $member = $this->project->members()->where('user_id', $user->id)->first();
        return $member && $member->canApprove();
    }

    public function submitForReview(): void
    {
        $this->update(['status' => 'review']);
        $this->createVersion('submitted_for_review');
    }

    public function approve(User $user, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => $notes
        ]);
        $this->createVersion('approved');
    }

    public function reject(User $user, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => $notes
        ]);
        $this->createVersion('rejected');
    }

    public function createVersion(string $changeType = 'edited', string $notes = null): ContentVersion
    {
        return $this->versions()->create([
            'created_by' => auth()->id(),
            'version_number' => $this->versions()->max('version_number') + 1,
            'content' => $this->content,
            'metadata' => $this->metadata,
            'change_notes' => $notes,
            'change_type' => $changeType
        ]);
    }

    public function restoreVersion(ContentVersion $version): void
    {
        $this->update([
            'content' => $version->content,
            'metadata' => $version->metadata,
            'version' => $this->version + 1
        ]);
        
        $this->createVersion('restored', "Restored to version {$version->version_number}");
    }

    // Accessor methods for tags and notes from metadata
    public function getTagsAttribute(): ?string
    {
        return $this->metadata['tags'] ?? null;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->metadata['notes'] ?? null;
    }

    // Mutator methods for tags and notes to metadata
    public function setTagsAttribute($value): void
    {
        $metadata = $this->metadata ?? [];
        if ($value) {
            $metadata['tags'] = $value;
        } else {
            unset($metadata['tags']);
        }
        $this->attributes['metadata'] = json_encode($metadata);
    }

    public function setNotesAttribute($value): void
    {
        $metadata = $this->metadata ?? [];
        if ($value) {
            $metadata['notes'] = $value;
        } else {
            unset($metadata['notes']);
        }
        $this->attributes['metadata'] = json_encode($metadata);
    }
}