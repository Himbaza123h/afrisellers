<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'locationX',
        'locationY',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the country that owns the addon.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all addon users for this addon.
     */
    public function addonUsers()
    {
        return $this->hasMany(AddonUser::class);
    }

    /**
     * Get active addon users for this addon.
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
     * Get users who have purchased this addon.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'addon_users')
            ->withPivot(['type', 'paid_at', 'paid_days', 'ended_at',
                'product_id', 'supplier_id', 'loadboad_id', 'car_id',
                'showroom_id', 'tradeshow_id'])
            ->withTimestamps();
    }

    /**
     * Scope a query to only include addons for a specific location.
     */
    public function scopeForLocation($query, $locationX, $locationY = null)
    {
        $query->where('locationX', $locationX);

        if ($locationY) {
            $query->where('locationY', $locationY);
        }

        return $query;
    }

    /**
     * Scope a query to only include addons for a specific country.
     */
    public function scopeForCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Check if addon is available for a specific country.
     */
    public function isAvailableForCountry($countryId)
    {
        return $this->country_id === null || $this->country_id === $countryId;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get location display name.
     */
    public function getLocationDisplayAttribute()
    {
        return ucfirst($this->locationX) . ' - ' . ucfirst(str_replace('_', ' ', $this->locationY));
    }
}
