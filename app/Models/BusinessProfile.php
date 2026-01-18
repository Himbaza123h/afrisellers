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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic Information
        'user_id',
        'country_id',
        'business_name',
        'logo',
        'cover_image',
        'business_registration_number',
        'business_license',
        'tax_id',
        'vendor_id',

        // Contact Information
        'phone',
        'phone_code',
        'whatsapp_number',
        'business_email',
        'city',
        'postal_code',
        'address',

        // Online Presence
        'website',
        'youtube_link',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
        'instagram_link',

        // Business Details
        'description',
        'year_established',
        'business_type',
        'certifications',
        'company_size',
        'annual_revenue',

        // Operations
        'export_markets',
        'production_capacity',
        'main_products',
        'quality_control',
        'payment_terms',
        'delivery_time',
        'minimum_order_value',

        // Contact Person
        'contact_person_name',
        'contact_person_position',
        'operating_hours',
        'languages_spoken',

        // Verification & Status
        'verification_status',
        'is_admin_verified',
        'is_verified_pro',
        'is_featured',
        'premium_expires_at',

        // Platform Metrics
        'response_time',
        'profile_completeness',
        'view_count',
        'last_active_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin_verified' => 'boolean',
        'is_verified_pro' => 'boolean',
        'is_featured' => 'boolean',
        'response_time' => 'integer',
        'profile_completeness' => 'integer',
        'view_count' => 'integer',
        'minimum_order_value' => 'decimal:2',
        'year_established' => 'integer',
        'premium_expires_at' => 'datetime',
        'last_active_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'business_license',
        'tax_id',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the user that owns the business profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the country that the business belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Relationship to vendor (created after admin verification)
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get addon users for this supplier
     */
    public function addonUsers()
    {
        return $this->hasMany(AddonUser::class, 'supplier_id');
    }

    /**
     * Get the products for the business profile through user.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id', 'user_id');
    }

    /**
     * Get the quote requests for the business profile.
     */
    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope a query to only include verified businesses.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope a query to only include pending businesses.
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    /**
     * Scope a query to only include rejected businesses.
     */
    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    /**
     * Scope a query to only include admin verified businesses.
     */
    public function scopeAdminVerified($query)
    {
        return $query->where('is_admin_verified', true);
    }

    /**
     * Scope a query to only include featured businesses.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include pro verified businesses.
     */
    public function scopeProVerified($query)
    {
        return $query->where('is_verified_pro', true);
    }

    /**
     * Scope a query to only include active premium businesses.
     */
    public function scopeActivePremium($query)
    {
        return $query->where('is_verified_pro', true)
                     ->where(function($q) {
                         $q->whereNull('premium_expires_at')
                           ->orWhere('premium_expires_at', '>', now());
                     });
    }

    // ============================================
    // VERIFICATION HELPER METHODS
    // ============================================

    /**
     * Check if business is verified
     */
    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if business is pending
     */
    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if business is rejected
     */
    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Verify the business
     */
    public function verify()
    {
        $this->update(['verification_status' => 'verified']);
    }

    /**
     * Reject the business
     */
    public function reject()
    {
        $this->update(['verification_status' => 'rejected']);
    }

    /**
     * Check if business has active premium.
     */
    public function hasActivePremium()
    {
        if (!$this->is_verified_pro) {
            return false;
        }

        if (is_null($this->premium_expires_at)) {
            return true;
        }

        return $this->premium_expires_at->isFuture();
    }

    // ============================================
    // VENDOR CREATION
    // ============================================

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

    // ============================================
    // ACCESSOR ATTRIBUTES
    // ============================================

    /**
     * Get the full phone number with code.
     */
    public function getFullPhoneAttribute()
    {
        return $this->phone_code . $this->phone;
    }

    /**
     * Get the logo URL or default.
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ?? asset('images/default-business-logo.png');
    }

    /**
     * Get the cover image URL or default.
     */
    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ?? asset('images/tea-field-banner.jpg');
    }

    /**
     * Get social media links as array.
     */
    public function getSocialLinksAttribute()
    {
        return array_filter([
            'facebook' => $this->facebook_link,
            'twitter' => $this->twitter_link,
            'linkedin' => $this->linkedin_link,
            'instagram' => $this->instagram_link,
            'youtube' => $this->youtube_link,
        ]);
    }

    /**
     * Get verification badge text.
     */
    public function getVerificationBadgeAttribute()
    {
        if ($this->is_admin_verified) {
            return 'Afrisellers Verified';
        }

        if ($this->is_verified_pro) {
            return 'Pro Verified';
        }

        return null;
    }

    // ============================================
    // UTILITY METHODS
    // ============================================

    /**
     * Check if business has social media presence.
     */
    public function hasSocialMedia()
    {
        return !empty($this->social_links);
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Calculate and update profile completeness.
     */
    public function updateProfileCompleteness()
    {
        $fields = [
            'business_name',
            'logo',
            'cover_image',
            'phone',
            'business_email',
            'city',
            'address',
            'description',
            'year_established',
            'business_type',
            'certifications',
            'company_size',
            'export_markets',
            'production_capacity',
            'main_products',
            'quality_control',
            'payment_terms',
            'delivery_time',
            'website',
            'contact_person_name',
        ];

        $filledFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        $completeness = round(($filledFields / count($fields)) * 100);
        $this->update(['profile_completeness' => $completeness]);

        return $completeness;
    }

    /**
     * Get profile completeness percentage
     */
    public function getCompletenessPercentage()
    {
        if ($this->profile_completeness) {
            return $this->profile_completeness;
        }

        return $this->updateProfileCompleteness();
    }

    // ============================================
    // MODEL EVENTS
    // ============================================

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Update last active on any update
        static::updating(function ($profile) {
            if ($profile->isDirty() && !$profile->isDirty('last_active_at')) {
                $profile->last_active_at = now();
            }
        });

        // Create vendor when admin verifies
        static::updated(function ($profile) {
            if ($profile->isDirty('is_admin_verified') && $profile->is_admin_verified) {
                $profile->createVendorOnVerification();
            }
        });
    }
}
