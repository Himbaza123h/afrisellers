<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessProfile;

class ProfileAnalytics extends Model
{
    protected $table = 'profile_analytics';

    protected $fillable = [
        'business_profile_id',
        'views', 'unique_visitors', 'shares',
        'likes', 'followers', 'contact_clicks',
        'whatsapp_clicks', 'website_clicks', 'rfq_count',
        'video_views', 'video_watch_time',
        'recorded_date', 'period',
    ];

    protected $casts = ['recorded_date' => 'date'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public static function alltime(int $profileId): self
    {
        return static::firstOrCreate(
            ['business_profile_id' => $profileId, 'period' => 'alltime', 'recorded_date' => null]
        );
    }

    public static function today(int $profileId): self
    {
        return static::firstOrCreate(
            ['business_profile_id' => $profileId, 'period' => 'daily', 'recorded_date' => now()->toDateString()]
        );
    }
}
