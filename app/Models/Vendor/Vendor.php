<?php

namespace App\Models\Vendor;

use App\Models\AddonUser;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Country;
use App\Models\OwnerID;
use App\Traits\Auditable;
use App\Models\UserPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{

    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = ['user_id', 'business_profile_id', 'owner_id_document_id', 'plan_id', 'email_verification_token', 'email_verified_at', 'account_status', 'email_verified'];

    protected $casts = [
        'email_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class, 'business_profile_id');
    }

    public function ownerID()
    {
        return $this->belongsTo(OwnerID::class, 'owner_id_document_id');
    }

    public function addonUsers()
    {
        return $this->hasMany(AddonUser::class, 'supplier_id');
    }




// Helper method to get country through business profile
public function country()
{
    return $this->hasOneThrough(
        Country::class,
        BusinessProfile::class,
        'id',              // Foreign key on business_profiles table
        'id',              // Foreign key on countries table
        'business_profile_id', // Local key on vendors table
        'country_id'       // Local key on business_profiles table
    );
}

// Get country name easily
public function getCountryName()
{
    return $this->businessProfile?->country?->name ?? 'N/A';
}

// Get country ID easily
public function getCountryId()
{
    return $this->businessProfile?->country_id;
}




    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('account_status', 'verified');
    }

    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('account_status', 'pending');
    }

    // Helper methods
    public function isVerified()
    {
        return $this->account_status === 'verified';
    }

    public function isActive()
    {
        return $this->account_status === 'active';
    }

    public function isPending()
    {
        return $this->account_status === 'pending';
    }

    public function verify()
    {
        $this->update(['account_status' => 'verified']);
    }

    public function reject()
    {
        $this->update(['account_status' => 'rejected']);
    }

    public function suspend()
    {
        $this->update(['account_status' => 'suspended']);
    }

    public function activate()
    {
        $this->update(['account_status' => 'active']);
    }
}
