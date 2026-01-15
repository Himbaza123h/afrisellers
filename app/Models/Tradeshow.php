<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tradeshow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'country_id',
        'tradeshow_number',
        'name',
        'slug',
        'description',
        'industry',
        'category',
        'venue_name',
        'venue_address',
        'city',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'timezone',
        'expected_visitors',
        'expected_exhibitors',
        'total_booths',
        'available_booths',
        'venue_size_sqm',
        'booth_price_from',
        'booth_price_to',
        'pricing_currency',
        'visitor_ticket_price',
        'free_entry',
        'registration_required',
        'registration_deadline',
        'registration_url',
        'website_url',
        'images',
        'banner_image',
        'logo_image',
        'videos',
        'documents',
        'features',
        'exhibitor_types',
        'target_audience',
        'special_attractions',
        'contact_name',
        'contact_email',
        'contact_phone',
        'organizer_name',
        'organizer_website',
        'status',
        'is_featured',
        'is_verified',
        'is_recurring',
        'recurrence_pattern',
        'views_count',
        'inquiries_count',
        'bookings_count',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'date',
        'images' => 'array',
        'videos' => 'array',
        'documents' => 'array',
        'features' => 'array',
        'exhibitor_types' => 'array',
        'target_audience' => 'array',
        'free_entry' => 'boolean',
        'registration_required' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_recurring' => 'boolean',
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
    public function getFullLocationAttribute()
    {
        return "{$this->city}, {$this->country->name}";
    }

    public function getDurationDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_date->isFuture();
    }

    public function getIsOngoingAttribute()
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function getIsCompletedAttribute()
    {
        return $this->end_date->isPast();
    }

    public function getPrimaryImageAttribute()
    {
        return $this->banner_image ?? ($this->images[0] ?? null);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tradeshow) {
            if (empty($tradeshow->tradeshow_number)) {
                $tradeshow->tradeshow_number = 'TS' . strtoupper(Str::random(8));
            }
            if (empty($tradeshow->slug)) {
                $tradeshow->slug = Str::slug($tradeshow->name);
            }
        });
    }
}
