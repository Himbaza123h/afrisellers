<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'min_qty',
        'max_qty',
        'price',
        'discount',
        'currency',
    ];

    protected $casts = [
        'min_qty' => 'integer',
        'max_qty' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the price.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get prices for a specific product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to order by quantity range.
     */
    public function scopeOrderedByQuantity($query)
    {
        return $query->orderBy('min_qty', 'asc');
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Get quantity range as string.
     */
    public function getQuantityRangeAttribute()
    {
        if ($this->max_qty) {
            return $this->min_qty . ' - ' . $this->max_qty . ' units';
        }
        return $this->min_qty . '+ units';
    }
}
