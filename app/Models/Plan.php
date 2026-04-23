<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $fillable = [
        'name',
        'price',
        'currency',
        'billing_cycle',
        'description',
        'featured_products',
        'product_limit',
        'buyer_inquiries_limit',
        'buyer_rfqs_limit',
        'has_ads',
        'negotiable',
        'is_default',
    ];

    protected $casts = [
        'price'                 => 'integer',
        'featured_products'     => 'boolean',
        'product_limit'         => 'integer',
        'buyer_inquiries_limit' => 'integer',
        'buyer_rfqs_limit'      => 'integer',
        'has_ads'               => 'boolean',
        'negotiable'            => 'boolean',
        'is_default'            => 'boolean',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured_products', true);
    }

    public function scopeWithAds($query)
    {
        return $query->where('has_ads', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isFree(): bool
    {
        return is_null($this->price) || $this->price === 0;
    }

    public function hasUnlimitedProducts(): bool
    {
        return is_null($this->product_limit);
    }

    public function formattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }

        return number_format($this->price) . ' ' . $this->currency . ' / ' . $this->billing_cycle;
    }
}
