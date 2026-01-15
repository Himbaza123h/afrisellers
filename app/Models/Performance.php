<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Performance extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'vendor_id',
        'country_id',
        'clicks',
        'impressions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'clicks' => 'integer',
        'impressions' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the product associated with the performance.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the vendor associated with the performance.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'vendor_id');
    }

    /**
     * Get the country associated with the performance.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Increment clicks for a performance record.
     */
    public function incrementClick(): void
    {
        $this->increment('clicks');
    }

    /**
     * Increment impressions for a performance record.
     */
    public function incrementImpression(): void
    {
        $this->increment('impressions');
    }

    /**
     * Calculate CTR (Click Through Rate).
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) {
            return 0.0;
        }

        return ($this->clicks / $this->impressions) * 100;
    }
}
