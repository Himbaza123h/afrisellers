<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendor\Vendor;
use App\Traits\Auditable;
use App\Models\Country;
use App\Models\OwnerID;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'user_id',
        'country_id',
        'business_name',
        'business_registration_number',
        'business_email',
        'phone',
        'phone_code',
        'city',
        'address',
        'postal_code',
        'website',
        'description',
        'youtube_link',
        'logo',
        'verification_status',
        'is_admin_verified',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }



    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    // Helper methods
    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }


    public function addonUsers()
{
    return $this->hasMany(AddonUser::class, 'supplier_id');
}


    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    public function verify()
    {
        $this->update(['verification_status' => 'verified']);
    }

    public function reject()
    {
        $this->update(['verification_status' => 'rejected']);
    }

    /**
     * Create vendor when admin verifies
     * This should be called when is_admin_verified is set to true
     */
    public function createVendorOnVerification()
    {
        if ($this->is_admin_verified && !$this->vendor_id) {
            // Get owner ID document for this user
            $ownerID = OwnerID::where('user_id', $this->user_id)->first();


            // Create vendor with user_id (only when admin verifies)
            // Set email as verified and account as active since admin verified it
            // Generate email verification token (6 digits)
            $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $vendor = Vendor::create([
                'user_id' => $this->user_id,
                'business_profile_id' => $this->id,
                'owner_id_document_id' => $ownerID?->id,
                'plan_id' => 1,
                'email_verification_token' => $verificationToken,
                'email_verified_at' => now(),
                'account_status' => 'active',
                'email_verified' => true,
            ]);

            $this->update(['vendor_id' => $vendor->id]);

            // Assign vendor role to user
            $vendorRole = \App\Models\Role::where('slug', 'vendor')->first();
            if ($vendorRole && !$this->user->hasRole('vendor')) {
                $this->user->assignRole($vendorRole);
            }

            return $vendor;
        }

        return null;
    }

    /**
     * Relationship to vendor (created after admin verification)
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
