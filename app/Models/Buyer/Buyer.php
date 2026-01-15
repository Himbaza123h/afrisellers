<?php

namespace App\Models\Buyer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Models\User;
use App\Models\Country;

class Buyer extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'user_id',
        'phone',
        'phone_code',
        'country_id',
        'city',
        'date_of_birth',
        'sex',
        'account_status',
        'email_verification_token',
        'email_verified',
        'email_verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->account_status === 'active';
    }

    public function isEmailVerified()
    {
        return $this->email_verified === true;
    }

    public function getFullPhoneAttribute()
    {
        return $this->phone_code . $this->phone;
    }
}
