<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\DB;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function regionalAdmins()
    {
        return $this->hasMany(RegionalAdmin::class);
    }

    public function activeAdmin()
    {
        return $this->hasOne(RegionalAdmin::class)->where('status', 'active');
    }

    // Get all business profiles in this region through countries
    public function businessProfiles()
    {
        return $this->hasManyThrough(
            BusinessProfile::class,
            Country::class,
            'region_id',    // Foreign key on countries table
            'country_id',   // Foreign key on business_profiles table
            'id',           // Local key on regions table
            'id'            // Local key on countries table
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getCountriesCountAttribute()
    {
        return $this->countries()->count();
    }

    // Methods
    public function getTotalVendors()
    {
        $countryIds = $this->countries->pluck('id');

        return DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->count();
    }

    public function getActiveVendors()
    {
        $countryIds = $this->countries->pluck('id');

        return DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->where('vendors.account_status', 'active')
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->count();
    }

    public function getMonthlyRevenue()
    {
        $countryIds = $this->countries->pluck('id');

        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.id');

        if (class_exists('\App\Models\Order')) {
            return \App\Models\Order::whereIn('vendor_id', $vendorIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total') ?? 0;
        }

        return 0;
    }

    public function getMonthlyOrders()
    {
        $countryIds = $this->countries->pluck('id');

        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.id');

        if (class_exists('\App\Models\Order')) {
            return \App\Models\Order::whereIn('vendor_id', $vendorIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        return 0;
    }

    public function getTotalOrders()
    {
        $countryIds = $this->countries->pluck('id');

        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.id');

        if (class_exists('\App\Models\Order')) {
            return \App\Models\Order::whereIn('vendor_id', $vendorIds)->count();
        }

        return 0;
    }
}
