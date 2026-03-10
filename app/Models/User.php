<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'provider',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function copywritingRequests()
    {
        return $this->hasMany(CopywritingRequest::class);
    }

    public function assignedCopywritingRequests()
    {
        return $this->hasMany(CopywritingRequest::class, 'assigned_to');
    }

    public function operatorProfile()
    {
        return $this->hasOne(OperatorProfile::class);
    }

    public function operatorOrders()
    {
        return $this->hasMany(Order::class, 'operator_id');
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function brandVoices()
    {
        return $this->hasMany(BrandVoice::class);
    }


    public function defaultBrandVoice()
    {
        return $this->hasOne(BrandVoice::class)->where('is_default', true);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function contentCalendars()
    {
        return $this->hasMany(ContentCalendar::class);
    }

    public function imageCaptions()
    {
        return $this->hasMany(ImageCaption::class);
    }

}
