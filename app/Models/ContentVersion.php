<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentVersion extends Model
{
    protected $fillable = [
        'content_id',
        'created_by',
        'version_number',
        'content',
        'metadata',
        'change_notes',
        'change_type'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(ProjectContent::class, 'content_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getChangeTypeLabel(): string
    {
        return match($this->change_type) {
            'created' => 'Created',
            'edited' => 'Edited',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'restored' => 'Restored',
            'submitted_for_review' => 'Submitted for Review',
            default => ucfirst($this->change_type)
        };
    }

    public function getChangeTypeColor(): string
    {
        return match($this->change_type) {
            'created' => 'bg-blue-100 text-blue-800',
            'edited' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'restored' => 'bg-purple-100 text-purple-800',
            'submitted_for_review' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}