<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'listing_number',
        'user_id',
        'make',
        'model',
        'year',
        'vehicle_type',
        'condition',
        'transmission',
        'fuel_type',
        'engine_capacity',
        'mileage',
        'color',
        'vin',
        'seats',
        'cargo_capacity',
        'cargo_capacity_unit',
        'from_city',
        'from_state',
        'from_country_id',
        'from_latitude',
        'from_longitude',
        'to_city',
        'to_state',
        'to_country_id',
        'to_latitude',
        'to_longitude',
        'flexible_destination',
        'preferred_routes',
        'price',
        'pricing_type',
        'currency',
        'price_negotiable',
        'available_from',
        'available_until',
        'features',
        'description',
        'images',
        'documents',
        'availability_status',
        'is_featured',
        'is_verified',
        'verified_at',
        'has_insurance',
        'insurance_expiry',
        'has_registration',
        'registration_expiry',
        'has_goods_transit_insurance',
        'permits',
        'driver_included',
        'driver_experience',
        'driver_languages',
        'accepted_cargo_types',
        'max_weight',
        'max_volume',
        'dimensions',
        'views_count',
        'inquiries_count',
        'completed_trips',
        'rating',
        'reviews_count',
        'listed_at',
        'last_trip_at',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'seats' => 'integer',
        'cargo_capacity' => 'decimal:2',
        'from_latitude' => 'decimal:8',
        'from_longitude' => 'decimal:8',
        'to_latitude' => 'decimal:8',
        'to_longitude' => 'decimal:8',
        'flexible_destination' => 'boolean',
        'preferred_routes' => 'array',
        'price' => 'decimal:2',
        'price_negotiable' => 'boolean',
        'available_from' => 'date',
        'available_until' => 'date',
        'features' => 'array',
        'images' => 'array',
        'documents' => 'array',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'has_insurance' => 'boolean',
        'insurance_expiry' => 'date',
        'has_registration' => 'boolean',
        'registration_expiry' => 'date',
        'has_goods_transit_insurance' => 'boolean',
        'permits' => 'array',
        'driver_included' => 'boolean',
        'driver_languages' => 'array',
        'accepted_cargo_types' => 'array',
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'dimensions' => 'array',
        'completed_trips' => 'integer',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'listed_at' => 'datetime',
        'last_trip_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'from_country_id');
    }

    public function toCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'to_country_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available')
            ->where(function($q) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', now());
            });
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnTrip($query)
    {
        return $query->where('availability_status', 'on_trip');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    public function scopeFromLocation($query, $countryId = null, $city = null)
    {
        return $query->when($countryId, function($q) use ($countryId) {
            $q->where('from_country_id', $countryId);
        })->when($city, function($q) use ($city) {
            $q->where('from_city', $city);
        });
    }

    public function scopeToLocation($query, $countryId = null, $city = null)
    {
        return $query->when($countryId, function($q) use ($countryId) {
            $q->where(function($query) use ($countryId) {
                $query->where('to_country_id', $countryId)
                      ->orWhere('flexible_destination', true);
            });
        })->when($city, function($q) use ($city) {
            $q->where(function($query) use ($city) {
                $query->where('to_city', $city)
                      ->orWhere('flexible_destination', true);
            });
        });
    }

    public function scopeFlexibleDestination($query)
    {
        return $query->where('flexible_destination', true);
    }

    public function scopePriceRange($query, $min = null, $max = null)
    {
        return $query->when($min, function($q) use ($min) {
            $q->where('price', '>=', $min);
        })->when($max, function($q) use ($max) {
            $q->where('price', '<=', $max);
        });
    }

    public function scopeWithCapacity($query, $minCapacity)
    {
        return $query->where('cargo_capacity', '>=', $minCapacity);
    }

    public function scopeAcceptsCargo($query, $cargoType)
    {
        return $query->whereJsonContains('accepted_cargo_types', $cargoType);
    }

    // Accessors
    public function getIsAvailableAttribute(): bool
    {
        return $this->availability_status === 'available' &&
               (!$this->available_until || $this->available_until >= now());
    }

    public function getIsOnTripAttribute(): bool
    {
        return $this->availability_status === 'on_trip';
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    public function getFromLocationAttribute(): string
    {
        $parts = array_filter([$this->from_city, $this->fromCountry->name ?? null]);
        return implode(', ', $parts);
    }

    public function getToLocationAttribute(): ?string
    {
        if ($this->flexible_destination) {
            return 'Flexible';
        }
        $parts = array_filter([$this->to_city, $this->toCountry->name ?? null]);
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function getRouteAttribute(): string
    {
        $from = $this->from_location;
        $to = $this->to_location ?? 'Any Destination';
        return "{$from} → {$to}";
    }

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return 'Negotiable';
        }
        return number_format($this->price, 2) . ' ' . $this->currency . ' (' . $this->pricing_type . ')';
    }

    public function getFormattedCapacityAttribute(): string
    {
        return number_format($this->cargo_capacity, 2) . ' ' . $this->cargo_capacity_unit;
    }

    public function getFormattedMileageAttribute(): string
    {
        return number_format($this->mileage) . ' km';
    }

    public function getPrimaryImageAttribute(): ?string
    {
        if (!$this->images || !is_array($this->images)) {
            return null;
        }
        return $this->images[0] ?? null;
    }

    public function getHasValidInsuranceAttribute(): bool
    {
        return $this->has_insurance &&
               $this->insurance_expiry &&
               $this->insurance_expiry >= now();
    }

    public function getHasValidRegistrationAttribute(): bool
    {
        return $this->has_registration &&
               $this->registration_expiry &&
               $this->registration_expiry >= now();
    }

    // Methods
    public function markAsOnTrip(): bool
    {
        return $this->update([
            'availability_status' => 'on_trip',
        ]);
    }

    public function markAsAvailable(): bool
    {
        return $this->update([
            'availability_status' => 'available',
        ]);
    }

    public function markAsMaintenance(): bool
    {
        return $this->update([
            'availability_status' => 'maintenance',
        ]);
    }

    public function completeTrip(): bool
    {
        return $this->update([
            'availability_status' => 'available',
            'last_trip_at' => now(),
            'completed_trips' => $this->completed_trips + 1,
        ]);
    }

    public function verify(): bool
    {
        return $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementInquiries(): void
    {
        $this->increment('inquiries_count');
    }

    public function toggleFeatured(): bool
    {
        return $this->update([
            'is_featured' => !$this->is_featured,
        ]);
    }

    public function updateRating(float $newRating): bool
    {
        $totalRating = ($this->rating * $this->reviews_count) + $newRating;
        $newCount = $this->reviews_count + 1;

        return $this->update([
            'rating' => $totalRating / $newCount,
            'reviews_count' => $newCount,
        ]);
    }

    public function canAcceptCargo(string $cargoType, float $weight = null): bool
    {
        $acceptedTypes = $this->accepted_cargo_types ?? [];

        if (!in_array($cargoType, $acceptedTypes) && !in_array('General', $acceptedTypes)) {
            return false;
        }

        if ($weight && $this->max_weight && $weight > $this->max_weight) {
            return false;
        }

        return true;
    }

    // Boot method for auto-generating listing_number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($car) {
            if (empty($car->listing_number)) {
                $car->listing_number = 'VEH-' . strtoupper(uniqid());
            }

            if (empty($car->listed_at)) {
                $car->listed_at = now();
            }
        });
    }
}
