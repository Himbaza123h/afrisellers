<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductAnalytics extends Model
{
    protected $table = 'product_analytics';

    protected $fillable = [
        'product_id', 'user_id',
        'views', 'unique_views', 'impressions', 'clicks',
        'likes', 'shares', 'wishlist_adds', 'cart_adds', 'rfq_count',
        'order_count', 'total_revenue',
        'review_count', 'avg_rating',
        'video_views', 'video_watch_time',
        'recorded_date', 'period',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'total_revenue' => 'decimal:2',
        'avg_rating'    => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Get or create the alltime row for a product
    public static function alltime(int $productId): self
    {
        return static::firstOrCreate(
            ['product_id' => $productId, 'period' => 'alltime', 'recorded_date' => null]
        );
    }

    // Get or create today's daily row
    public static function today(int $productId): self
    {
        return static::firstOrCreate(
            ['product_id' => $productId, 'period' => 'daily', 'recorded_date' => now()->toDateString()]
        );
    }
}
