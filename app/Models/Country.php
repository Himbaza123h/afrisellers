<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductCategory;
use App\Models\Vendor\Vendor;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Country extends Model
{
    use HasFactory, SoftDeletes, HasRelationships;

    protected $fillable = [
        'name',
        'code',
        'flag_url',
        'image',
        'status',
        'region_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function businessProfiles()
    {
        return $this->hasMany(BusinessProfile::class, 'country_id');
    }

    // Get vendors through business profiles
    public function vendors()
    {
        return $this->hasManyThrough(
            Vendor::class,
            BusinessProfile::class,
            'country_id',           // Foreign key on business_profiles table
            'business_profile_id',  // Foreign key on vendors table
            'id',                   // Local key on countries table
            'id'                    // Local key on business_profiles table
        );
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'country_product_category');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeInRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeWithFlag($query)
    {
        return $query->whereNotNull('flag_url')->where('flag_url', '!=', '');
    }

    public function scopeWithoutFlag($query)
    {
        return $query->where(function($q) {
            $q->whereNull('flag_url')->orWhere('flag_url', '');
        });
    }

public function products()
{
    return \App\Models\Product::query()
        ->whereHas('user.vendor.businessProfile', function($query) {
            $query->where('country_id', $this->id);
        });
}

public function orders()
{
    return \App\Models\Order::query()
        ->whereHas('vendor.businessProfile', function($query) {
            $query->where('country_id', $this->id);
        });
}

    // Accessors
    public function getHasFlagAttribute()
    {
        return !empty($this->flag_url);
    }

    public function getVendorsCountTextAttribute()
    {
        $count = $this->getVendorsCount();
        return $count . ' ' . str_plural('vendor', $count);
    }

    public function getShortCodeAttribute()
    {
        return $this->code ?? strtoupper(substr($this->name, 0, 2));
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function activate()
    {
        return $this->update(['status' => 'active']);
    }

    public function deactivate()
    {
        return $this->update(['status' => 'inactive']);
    }

    public function toggleStatus()
    {
        return $this->update([
            'status' => $this->status === 'active' ? 'inactive' : 'active'
        ]);
    }

    public function hasRegion()
    {
        return $this->region_id !== null;
    }

    public function getRegionName()
    {
        return $this->region?->name ?? 'Not assigned';
    }

    // Statistical methods
    public function getVendorsCount()
    {
        return Vendor::whereHas('businessProfile', function($q) {
            $q->where('country_id', $this->id);
        })->count();
    }

    public function getTotalVendors()
    {
        return $this->getVendorsCount();
    }

    public function getActiveVendors()
    {
        return Vendor::whereHas('businessProfile', function($q) {
            $q->where('country_id', $this->id);
        })->where('account_status', 'active')->count();
    }

    public function getTotalOrders()
    {
        return \App\Models\Order::whereHas('vendor.businessProfile', function($q) {
            $q->where('country_id', $this->id);
        })->count();
    }

    public function getMonthlyRevenue()
    {
        return \App\Models\Order::whereHas('vendor.businessProfile', function($q) {
            $q->where('country_id', $this->id);
        })->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->sum('total') ?? 0;
    }

    // Static helper methods
    public static function getActiveCount()
    {
        return static::where('status', 'active')->count();
    }

    public static function getInactiveCount()
    {
        return static::where('status', 'inactive')->count();
    }

    public static function getCountriesWithFlags()
    {
        return static::whereNotNull('flag_url')
            ->where('flag_url', '!=', '')
            ->count();
    }

    public static function getCountriesByRegion($regionId)
    {
        return static::where('region_id', $regionId)
            ->where('status', 'active')
            ->get();
    }
}
