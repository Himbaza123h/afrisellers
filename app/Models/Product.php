<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendor\Vendor;
use App\Traits\Auditable;
use App\Models\ProductCategory;
use App\Models\Country;
use Illuminate\Support\Str;

class Product extends Model
{
    use Auditable;

    protected $fillable = ['user_id', 'country_id', 'product_category_id', 'views', 'name', 'slug', 'description', 'short_description', 'min_order_quantity', 'is_negotiable', 'overview', 'specifications', 'status', 'is_admin_verified'];

    protected $casts = [
        'specifications' => 'array',
        'is_negotiable' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

// Replace the showrooms relationship with this:
public function showrooms()
{
    return $this->belongsToMany(Showroom::class, 'showroom_product')
                ->withPivot('added_at')
                ->withTimestamps();
}

/**
 * Get addon users for this product.
 */
public function addonUsers()
{
    return $this->hasMany(AddonUser::class, 'product_id');
}


public function performances()
{
    return $this->hasMany(Performance::class);
}

public function showroomProducts()
{
    return $this->hasMany(ShowroomProduct::class);
}

/**
 * Get the promo codes applicable to this product.
 */
public function promoCodes()
{
    return $this->belongsToMany(PromoCode::class, 'promo_code_product')
                ->withTimestamps();
}

    /**
     * Get the prices for the product.
     */
    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductUserReview::class)->where('status', true);
    }
}
