<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'contact_name',
        'phone',
        'email',
        'company_name',
        'address_line1',
        'address_line2',
        'city',
        'state_province',
        'postal_code',
        'country_id',
        'latitude',
        'longitude',
        'is_default',
        'delivery_instructions',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'country_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_default' => 'boolean',
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

    public function shippingOrders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function billingOrders()
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    // Scopes
    public function scopeShipping($query)
    {
        return $query->whereIn('type', ['shipping', 'both']);
    }

    public function scopeBilling($query)
    {
        return $query->whereIn('type', ['billing', 'both']);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country?->name,
        ]);

        return implode(', ', $parts);
    }

    public function getShortAddressAttribute()
    {
        return $this->address_line1 . ', ' . $this->city;
    }

    public function getFormattedAddressAttribute()
    {
        $html = "<strong>{$this->contact_name}</strong><br>";

        if ($this->company_name) {
            $html .= "{$this->company_name}<br>";
        }

        $html .= "{$this->address_line1}<br>";

        if ($this->address_line2) {
            $html .= "{$this->address_line2}<br>";
        }

        $html .= "{$this->city}";

        if ($this->state_province) {
            $html .= ", {$this->state_province}";
        }

        if ($this->postal_code) {
            $html .= " {$this->postal_code}";
        }

        $html .= "<br>{$this->country?->name}<br>";
        $html .= "Phone: {$this->phone}";

        if ($this->email) {
            $html .= "<br>Email: {$this->email}";
        }

        return $html;
    }

    public function getDisplayLabelAttribute()
    {
        return $this->label ?: ucfirst($this->type);
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'shipping' => 'primary',
            'billing' => 'success',
            'both' => 'info',
            default => 'secondary'
        };
    }

    public function getHasCoordinatesAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getGoogleMapsLinkAttribute()
    {
        if (!$this->has_coordinates) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function getIsShippingAttribute()
    {
        return in_array($this->type, ['shipping', 'both']);
    }

    public function getIsBillingAttribute()
    {
        return in_array($this->type, ['billing', 'both']);
    }

    // Methods
    public function makeDefault()
    {
        // Remove default from other addresses of same user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function removeDefault()
    {
        $this->update(['is_default' => false]);
    }

    public function setCoordinates($latitude, $longitude)
    {
        $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }

    public function distanceTo($latitude, $longitude)
    {
        if (!$this->has_coordinates) {
            return null;
        }

        // Haversine formula to calculate distance in kilometers
        $earthRadius = 6371;

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function canBeUsedForShipping()
    {
        return $this->is_shipping;
    }

    public function canBeUsedForBilling()
    {
        return $this->is_billing;
    }

    // Static methods
    public static function types()
    {
        return [
            'shipping' => 'Shipping Only',
            'billing' => 'Billing Only',
            'both' => 'Shipping & Billing'
        ];
    }

    public static function getDefaultForUser($userId, $type = 'both')
    {
        return static::byUser($userId)
            ->where('type', $type)
            ->default()
            ->first();
    }

    public static function getShippingAddressesForUser($userId)
    {
        return static::byUser($userId)->shipping()->get();
    }

    public static function getBillingAddressesForUser($userId)
    {
        return static::byUser($userId)->billing()->get();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            // If this is the first address for user, make it default
            $userAddressCount = static::where('user_id', $address->user_id)->count();

            if ($userAddressCount === 0) {
                $address->is_default = true;
            }

            // If marked as default, remove default from others
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->update(['is_default' => false]);
            }
        });

        static::updating(function ($address) {
            // If marked as default, remove default from others
            if ($address->isDirty('is_default') && $address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
