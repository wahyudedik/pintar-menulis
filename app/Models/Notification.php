<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    // Notification types
    const TYPE_ORDER_NEW = 'order_new';
    const TYPE_ORDER_ACCEPTED = 'order_accepted';
    const TYPE_ORDER_REJECTED = 'order_rejected';
    const TYPE_ORDER_COMPLETED = 'order_completed';
    const TYPE_ORDER_REVISION = 'order_revision';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_PAYMENT_VERIFIED = 'payment_verified';
    const TYPE_PAYMENT_REJECTED = 'payment_rejected';
    const TYPE_WITHDRAWAL_APPROVED = 'withdrawal_approved';
    const TYPE_WITHDRAWAL_REJECTED = 'withdrawal_rejected';
    const TYPE_WITHDRAWAL_COMPLETED = 'withdrawal_completed';
    const TYPE_FEEDBACK_NEW = 'feedback_new';
    const TYPE_FEEDBACK_RESPONSE = 'feedback_response';
    const TYPE_SUBSCRIPTION_ACTIVATED = 'subscription_activated';
    const TYPE_SUBSCRIPTION_PENDING = 'subscription_pending';
    const TYPE_SUBSCRIPTION_REJECTED = 'subscription_rejected';
    const TYPE_PAYMENT_RELEASED = 'payment_released';
    const TYPE_ORDER_DISPUTED = 'order_disputed';
    const TYPE_PROJECT_INVITATION = 'project_invitation';
    const TYPE_PROJECT_INVITATION_ACCEPTED = 'project_invitation_accepted';
    const TYPE_PROJECT_INVITATION_DECLINED = 'project_invitation_declined';
    const TYPE_CONTENT_SUBMITTED = 'content_submitted';
    const TYPE_CONTENT_APPROVED = 'content_approved';
    const TYPE_CONTENT_REJECTED = 'content_rejected';
    const TYPE_OPERATOR_VERIFIED = 'operator_verified';
    const TYPE_OPERATOR_UNVERIFIED = 'operator_unverified';
    const TYPE_WITHDRAWAL_SUBMITTED = 'withdrawal_submitted';
    const TYPE_PAYMENT_PROOF_SUBMITTED = 'payment_proof_submitted';
}
