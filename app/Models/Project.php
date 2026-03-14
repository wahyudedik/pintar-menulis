<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'business_description',
        'target_audience',
        'brand_tone',
        'preferred_platforms',
    ];

    protected $casts = [
        'preferred_platforms' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // 👥 Collaboration Features
    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function acceptedMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class)->where('status', 'accepted');
    }

    public function pendingMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class)->where('status', 'pending');
    }

    public function content(): HasMany
    {
        return $this->hasMany(ProjectContent::class);
    }

    public function drafts(): HasMany
    {
        return $this->hasMany(ProjectContent::class)->where('status', 'draft');
    }

    public function pendingReview(): HasMany
    {
        return $this->hasMany(ProjectContent::class)->where('status', 'review');
    }

    public function approvedContent(): HasMany
    {
        return $this->hasMany(ProjectContent::class)->where('status', 'approved');
    }

    // Helper methods for collaboration
    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getMemberRole(User $user): ?string
    {
        if ($this->isOwner($user)) {
            return 'owner';
        }

        $member = $this->members()->where('user_id', $user->id)->where('status', 'accepted')->first();
        return $member?->role;
    }

    public function canUserAccess(User $user): bool
    {
        return $this->isOwner($user) || $this->hasMember($user);
    }

    public function canUserEdit(User $user): bool
    {
        if ($this->isOwner($user)) {
            return true;
        }

        $member = $this->members()->where('user_id', $user->id)->where('status', 'accepted')->first();
        return $member && $member->canEdit();
    }

    public function canUserApprove(User $user): bool
    {
        if ($this->isOwner($user)) {
            return true;
        }

        $member = $this->members()->where('user_id', $user->id)->where('status', 'accepted')->first();
        return $member && $member->canApprove();
    }

    public function inviteMember(string $email, string $role, User $invitedBy): ?ProjectMember
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return null; // User not found
        }

        if ($this->hasMember($user) || $this->isOwner($user)) {
            return null; // Already a member or owner
        }

        return $this->members()->create([
            'user_id' => $user->id,
            'role' => $role,
            'status' => 'pending',
            'invited_at' => now(),
            'invited_by' => $invitedBy->id
        ]);
    }

    public function getTotalMembers(): int
    {
        return $this->acceptedMembers()->count() + 1; // +1 for owner
    }

    public function getPendingApprovals(): int
    {
        return $this->pendingReview()->count();
    }
}
