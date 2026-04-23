<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\Vendor;

class VendorAnalytics extends Model
{
    protected $table = 'vendor_analytics';

    protected $fillable = [
        'vendor_id',
        'store_visits', 'unique_visitors',
        'total_product_views', 'total_impressions',
        'total_orders', 'completed_orders', 'total_revenue',
        'total_customers', 'repeat_customers', 'conversion_rate',
        'rfq_count', 'total_likes', 'total_shares', 'followers',
        'video_views', 'video_watch_time',
        'recorded_date', 'period',
    ];

    protected $casts = [
        'recorded_date'   => 'date',
        'total_revenue'   => 'decimal:2',
        'conversion_rate' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public static function alltime(int $vendorId): self
    {
        return static::firstOrCreate(
            ['vendor_id' => $vendorId, 'period' => 'alltime', 'recorded_date' => null]
        );
    }

    public static function today(int $vendorId): self
    {
        return static::firstOrCreate(
            ['vendor_id' => $vendorId, 'period' => 'daily', 'recorded_date' => now()->toDateString()]
        );
    }
}
