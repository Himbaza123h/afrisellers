<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'sku',
        'quantity',
        'unit_price',
        'subtotal',
        'tax',
        'total',
        'attributes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'attributes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getFormattedUnitPriceAttribute()
    {
        return $this->order->currency . ' ' . number_format($this->unit_price, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return $this->order->currency . ' ' . number_format($this->subtotal, 2);
    }

    public function getFormattedTotalAttribute()
    {
        return $this->order->currency . ' ' . number_format($this->total, 2);
    }

    public function getFormattedAttributesAttribute()
    {
        if (!$this->attributes) {
            return null;
        }

        return collect($this->attributes)
            ->map(function ($value, $key) {
                return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
            })
            ->implode(', ');
    }

    public function getHasAttributesAttribute()
    {
        return !empty($this->attributes);
    }

    // Methods
    public function calculateSubtotal()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        return $this->subtotal;
    }

    public function calculateTotal()
    {
        $this->total = $this->subtotal + $this->tax;
        return $this->total;
    }

    public function calculateAll()
    {
        $this->calculateSubtotal();
        $this->calculateTotal();
        $this->save();

        return $this;
    }

    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->calculateAll();

        // Recalculate order totals
        $this->order->calculateTotals();
    }

    public function getAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return parent::getAttribute($key);
    }

    // Boot method to auto-calculate on save
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->isDirty(['quantity', 'unit_price'])) {
                $item->calculateSubtotal();
                $item->calculateTotal();
            }
        });

        static::saved(function ($item) {
            // Recalculate order totals when item changes
            if ($item->order) {
                $item->order->calculateTotals();
            }
        });

        static::deleted(function ($item) {
            // Recalculate order totals when item is deleted
            if ($item->order) {
                $item->order->calculateTotals();
            }
        });
    }
}
