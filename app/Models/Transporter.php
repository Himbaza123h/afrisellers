<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transporter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'registration_number',
        'license_number',
        'phone',
        'email',
        'address',
        'country_id',
        'service_areas',
        'vehicle_types',
        'fleet_size',
        'is_verified',
        'verified_at',
        'average_rating',
        'total_deliveries',
        'successful_deliveries',
        'status',
        'documents',
    ];

    protected $casts = [
        'service_areas' => 'array',
        'vehicle_types' => 'array',
        'documents' => 'array',
        'fleet_size' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'average_rating' => 'decimal:2',
        'total_deliveries' => 'integer',
        'successful_deliveries' => 'integer',
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeWithMinRating($query, $rating)
    {
        return $query->where('average_rating', '>=', $rating);
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

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function isVerified()
    {
        return $this->is_verified === true;
    }

    public function activate()
    {
        return $this->update([
            'status' => 'active',
        ]);
    }

    public function deactivate()
    {
        return $this->update([
            'status' => 'inactive',
        ]);
    }

    public function suspend()
    {
        return $this->update([
            'status' => 'suspended',
        ]);
    }

    public function verify()
    {
        return $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function unverify()
    {
        return $this->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    // Performance metrics
    public function getSuccessRateAttribute()
    {
        if ($this->total_deliveries == 0) {
            return 0;
        }
        return round(($this->successful_deliveries / $this->total_deliveries) * 100, 1);
    }

    public function getFailedDeliveriesAttribute()
    {
        return $this->total_deliveries - $this->successful_deliveries;
    }

    public function addDelivery($successful = true)
    {
        $this->increment('total_deliveries');

        if ($successful) {
            $this->increment('successful_deliveries');
        }
    }

    public function updateRating($newRating)
    {
        // Simple average calculation
        // You might want to implement a more sophisticated rating system
        $totalRatings = $this->total_deliveries;
        $currentTotal = $this->average_rating * ($totalRatings - 1);
        $newAverage = ($currentTotal + $newRating) / $totalRatings;

        return $this->update([
            'average_rating' => round($newAverage, 2),
        ]);
    }

    // Accessors
    public function getServiceAreasListAttribute()
    {
        return is_array($this->service_areas) ? implode(', ', $this->service_areas) : 'N/A';
    }

    public function getVehicleTypesListAttribute()
    {
        return is_array($this->vehicle_types) ? implode(', ', $this->vehicle_types) : 'N/A';
    }

    public function getRatingStarsAttribute()
    {
        $rating = $this->average_rating;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return [
            'full' => $fullStars,
            'half' => $halfStar,
            'empty' => $emptyStars,
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => ['text' => 'Active', 'class' => 'bg-green-100 text-green-800'],
            'inactive' => ['text' => 'Inactive', 'class' => 'bg-gray-100 text-gray-800'],
            'suspended' => ['text' => 'Suspended', 'class' => 'bg-red-100 text-red-800'],
        ];

        return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'bg-gray-100 text-gray-800'];
    }

    // Document helpers
    public function hasDocument($type)
    {
        return isset($this->documents[$type]) && !empty($this->documents[$type]);
    }

    public function getDocument($type)
    {
        return $this->documents[$type] ?? null;
    }

    public function addDocument($type, $path)
    {
        $documents = $this->documents ?? [];
        $documents[$type] = $path;

        return $this->update(['documents' => $documents]);
    }

    public function removeDocument($type)
    {
        $documents = $this->documents ?? [];
        unset($documents[$type]);

        return $this->update(['documents' => $documents]);
    }
}
