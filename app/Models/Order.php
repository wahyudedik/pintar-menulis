<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'project_id',
        'operator_id',
        'category',
        'brief',
        'budget',
        'deadline',
        'result',
        'operator_notes',
        'rating',
        'review',
        'revision_notes',
        'dispute_reason',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'used_caption_quota',
        'used_product_description_quota',
        'earnings_added',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
        'budget' => 'integer',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function copywritingRequests()
    {
        return $this->hasMany(CopywritingRequest::class);
    }

    public function getRemainingCaptionQuotaAttribute()
    {
        return $this->package->caption_quota - $this->used_caption_quota;
    }

    public function getRemainingProductDescriptionQuotaAttribute()
    {
        return $this->package->product_description_quota - $this->used_product_description_quota;
    }

    public function trainingData()
    {
        return $this->hasOne(MLTrainingData::class);
    }

    public function revisions()
    {
        return $this->hasMany(OrderRevision::class)->orderBy('revision_number', 'asc');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
