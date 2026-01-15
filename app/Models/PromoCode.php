<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'user_usage_limit',
        'start_date',
        'end_date',
        'applicable_to',
        'status',
        'currency',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'user_usage_limit' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the user who created the promo code.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the products associated with this promo code.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'promo_code_product')
                    ->withTimestamps();
    }

    /**
     * Get the usage records for this promo code.
     */
    public function usages()
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    /**
     * Check if promo code is valid.
     */
    public function isValid()
    {
        $now = Carbon::now();

        return $this->status === 'active'
            && $this->start_date <= $now
            && $this->end_date >= $now
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Check if user can use this promo code.
     */
    public function canBeUsedByUser($userId)
    {
        if ($this->user_usage_limit === null) {
            return true;
        }

        $userUsageCount = $this->usages()->where('user_id', $userId)->count();

        return $userUsageCount < $this->user_usage_limit;
    }

    /**
     * Calculate discount amount for given price.
     */
    public function calculateDiscount($price)
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($price * $this->discount_value) / 100;

            if ($this->max_discount_amount !== null) {
                $discount = min($discount, $this->max_discount_amount);
            }

            return $discount;
        }

        // Fixed discount
        return min($this->discount_value, $price);
    }

    /**
     * Scope for active promo codes.
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();

        return $query->where('status', 'active')
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Scope for available promo codes (not exhausted).
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereColumn('usage_count', '<', 'usage_limit');
        });
    }
}
