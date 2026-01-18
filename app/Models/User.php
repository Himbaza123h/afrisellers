<?php

namespace App\Models;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Buyer\Buyer;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['name', 'email', 'country_admin', 'country_id','regional_id','regional_admin','agent', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function products()
{
    return $this->hasMany(Product::class, 'user_id');
}

/**
 * Get all addon users for this user.
 */
public function addonUsers()
{
    return $this->hasMany(AddonUser::class);
}

public function country()
{
    return $this->belongsTo(Country::class);
}

/**
 * Get active addon users for this user.
 */
public function activeAddonUsers()
{
    return $this->hasMany(AddonUser::class)
        ->whereNotNull('paid_at')
        ->where(function ($query) {
            $query->whereNull('ended_at')
                ->orWhere('ended_at', '>', now());
        });
}

/**
 * Get addons through addon_users pivot table.
 */
public function addons()
{
    return $this->belongsToMany(Addon::class, 'addon_users')
        ->withPivot(['type', 'paid_at', 'paid_days', 'ended_at',
            'product_id', 'supplier_id', 'loadboad_id', 'car_id',
            'showroom_id', 'tradeshow_id'])
        ->withTimestamps();
}

/**
 * Get active addons for this user.
 */
public function activeAddons()
{
    return $this->belongsToMany(Addon::class, 'addon_users')
        ->wherePivotNotNull('paid_at')
        ->where(function ($query) {
            $query->whereNull('addon_users.ended_at')
                ->orWhere('addon_users.ended_at', '>', now());
        })
        ->withPivot(['type', 'paid_at', 'paid_days', 'ended_at',
            'product_id', 'supplier_id', 'loadboad_id', 'car_id',
            'showroom_id', 'tradeshow_id'])
        ->withTimestamps();
}

/**
 * Check if user has active addon for specific item.
 */
public function hasActiveAddonFor($type, $relatedId)
{
    return $this->addonUsers()
        ->where('type', $type)
        ->where($type . '_id', $relatedId)
        ->whereNotNull('paid_at')
        ->where(function ($query) {
            $query->whereNull('ended_at')
                ->orWhere('ended_at', '>', now());
        })
        ->exists();
}

/**
 * Get addon users by type.
 */
public function addonUsersByType($type)
{
    return $this->addonUsers()->where('type', $type);
}

/**
 * Get active product addons.
 */
public function activeProductAddons()
{
    return $this->addonUsers()
        ->where('type', 'product')
        ->whereNotNull('paid_at')
        ->where(function ($query) {
            $query->whereNull('ended_at')
                ->orWhere('ended_at', '>', now());
        })
        ->with('product');
}

/**
 * Get active showroom addons.
 */
public function activeShowroomAddons()
{
    return $this->addonUsers()
        ->where('type', 'showroom')
        ->whereNotNull('paid_at')
        ->where(function ($query) {
            $query->whereNull('ended_at')
                ->orWhere('ended_at', '>', now());
        })
        ->with('showroom');
}

    // Helper methods
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return $this->roles->contains($role);
    }

    public function businessProfile()
{
    return $this->hasOne(BusinessProfile::class);
}

public function commissions()
{
    return $this->hasMany(Commission::class);
}

public function escrowsAsBuyer()
{
    return $this->hasMany(Escrow::class, 'buyer_id');
}

public function escrowsAsVendor()
{
    return $this->hasMany(Escrow::class, 'vendor_id');
}

    public function hasPermission($permission)
    {
        // Check direct permissions
        if ($this->permissions->contains('slug', $permission)) {
            return true;
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        return $this->roles()->syncWithoutDetaching($role);
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        return $this->roles()->detach($role);
    }

    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }
        return $this->permissions()->syncWithoutDetaching($permission);
    }

    public function isBuyer()
    {
        return $this->hasRole('buyer');
    }

    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }
        return $this->permissions()->detach($permission);
    }

    public function isVendor()
    {
        return $this->vendor()->exists();
    }
    public function plan()
    {
        return $this->hasOneThrough(Subscription::class, Vendor::class, 'user_id', 'id', 'id', 'plan_id');
    }

    public function regionalAdmin()
{
    return $this->hasOne(RegionalAdmin::class);
}


/**
 * Get all user plans
 */
public function userPlans()
{
    return $this->hasMany(UserPlan::class);
}

/**
 * Get the current active plan
 */
public function currentPlan()
{
    return $this->hasOne(UserPlan::class)
        ->where('status', 'active')
        ->where('expires_at', '>', now())
        ->latest();
}

/**
 * Check if user has an active plan
 */
public function hasActivePlan()
{
    return $this->currentPlan()->exists();
}

/**
 * Get the active plan
 */
public function getActivePlan()
{
    return $this->currentPlan()->with('plan')->first();
}

/**
 * Check if user can add more products
 */
public function canAddProduct()
{
    $activePlan = $this->getActivePlan();

    if (!$activePlan) {
        return false;
    }

    return $activePlan->canAddProduct();
}

/**
 * Check if user can send inquiries
 */
public function canSendInquiry()
{
    $activePlan = $this->getActivePlan();

    if (!$activePlan) {
        return false;
    }

    return $activePlan->canSendInquiry();
}

/**
 * Check if user can send RFQs
 */
public function canSendRfq()
{
    $activePlan = $this->getActivePlan();

    if (!$activePlan) {
        return false;
    }

    return $activePlan->canSendRfq();
}

/**
 * Get plan feature value
 */
public function getPlanFeature($feature)
{
    $activePlan = $this->getActivePlan();

    if (!$activePlan) {
        return null;
    }

    return $activePlan->plan->$feature ?? null;
}

public function managedRegions()
{
    return $this->hasManyThrough(Region::class, RegionalAdmin::class, 'user_id', 'id', 'id', 'region_id')
        ->where('regional_admins.status', 'active');
}

// Helper methods (add to existing helper methods)
public function isRegionalAdmin()
{
    return $this->regionalAdmin()->where('status', 'active')->exists();
}

public function getManagedRegion()
{
    return $this->regionalAdmin()->with('region')->first()?->region;
}

public function canManageRegion($regionId)
{
    return $this->regionalAdmin()
        ->where('region_id', $regionId)
        ->where('status', 'active')
        ->exists();
}
}
