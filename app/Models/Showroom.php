<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Support\Str;

class Showroom extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'user_id',
        'country_id',
        'showroom_number',
        'name',
        'slug',
        'description',
        'business_type',
        'industry',
        'product_categories',
        'address',
        'city',
        'state_province',
        'postal_code',
        'latitude',
        'longitude',
        'operating_hours',
        'open_weekends',
        'appointment_required',
        'walk_ins_welcome',
        'showroom_size_sqm',
        'display_capacity',
        'current_inventory',
        'has_service_center',
        'has_parts_department',
        'has_financing',
        'services',
        'brands_carried',
        'vehicle_types',
        'new_vehicles',
        'used_vehicles',
        'images',
        'primary_image',
        'logo_image',
        'videos',
        'virtual_tour_url',
        'facilities',
        'has_parking',
        'parking_spaces',
        'wheelchair_accessible',
        'contact_person',
        'email',
        'phone',
        'alternate_phone',
        'whatsapp',
        'website_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'business_license',
        'established_date',
        'years_in_business',
        'employees_count',
        'certifications',
        'languages_spoken',
        'status',
        'is_featured',
        'is_verified',
        'is_authorized_dealer',
        'views_count',
        'inquiries_count',
        'visits_count',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'product_categories' => 'array',
        'operating_hours' => 'array',
        'services' => 'array',
        'brands_carried' => 'array',
        'vehicle_types' => 'array',
        'images' => 'array',
        'videos' => 'array',
        'facilities' => 'array',
        'certifications' => 'array',
        'languages_spoken' => 'array',
        'open_weekends' => 'boolean',
        'appointment_required' => 'boolean',
        'walk_ins_welcome' => 'boolean',
        'has_service_center' => 'boolean',
        'has_parts_department' => 'boolean',
        'has_financing' => 'boolean',
        'new_vehicles' => 'boolean',
        'used_vehicles' => 'boolean',
        'has_parking' => 'boolean',
        'wheelchair_accessible' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_authorized_dealer' => 'boolean',
        'established_date' => 'date',
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

    // Accessors
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->country->name}";
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'showroom_product')
                    ->withPivot('added_at')
                    ->withTimestamps();
    }

    public function showroomProducts()
    {
        return $this->hasMany(ShowroomProduct::class);
    }

    public function getIsOpenNowAttribute()
    {
        // You can implement logic to check if showroom is currently open
        // based on operating_hours and current time
        return true; // Placeholder
    }

    public function getTodayHoursAttribute()
    {
        $day = strtolower(now()->format('l'));
        return $this->operating_hours[$day] ?? 'Closed';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeAuthorizedDealer($query)
    {
        return $query->where('is_authorized_dealer', true);
    }

    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($showroom) {
            if (empty($showroom->showroom_number)) {
                $showroom->showroom_number = 'SR' . strtoupper(Str::random(8));
            }
            if (empty($showroom->slug)) {
                $showroom->slug = Str::slug($showroom->name);
            }
        });
    }
}
