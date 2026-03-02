<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'project_id',
        'start_date',
        'end_date',
        'status',
        'used_caption_quota',
        'used_product_description_quota',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
}
