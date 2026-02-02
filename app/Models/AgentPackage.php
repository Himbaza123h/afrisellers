<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AgentPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'duration_days',
        'max_referrals',
        'allow_rfqs',
        'priority_support',
        'advanced_analytics',
        'commission_boost',
        'commission_rate',
        'featured_profile',
        'max_payouts_per_month',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'allow_rfqs' => 'boolean',
        'priority_support' => 'boolean',
        'advanced_analytics' => 'boolean',
        'commission_boost' => 'boolean',
        'featured_profile' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Boot method to generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(AgentSubscription::class, 'package_id');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(AgentSubscription::class, 'package_id')
            ->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Helper Methods
    public function isPopular()
    {
        return $this->subscriptions()->count() > 10;
    }

    public function getMonthlyPrice()
    {
        return match($this->billing_cycle) {
            'monthly' => $this->price,
            'quarterly' => $this->price / 3,
            'yearly' => $this->price / 12,
            default => $this->price,
        };
    }

    public function getFeatures()
    {
        return [
            'max_referrals' => $this->max_referrals,
            'allow_rfqs' => $this->allow_rfqs,
            'priority_support' => $this->priority_support,
            'advanced_analytics' => $this->advanced_analytics,
            'commission_boost' => $this->commission_boost,
            'commission_rate' => $this->commission_rate,
            'featured_profile' => $this->featured_profile,
            'max_payouts_per_month' => $this->max_payouts_per_month,
        ];
    }

    public function getBadgeColorAttribute()
    {
        return match(strtolower($this->name)) {
            'premium' => 'purple',
            'gold' => 'yellow',
            'silver' => 'gray',
            'normal', 'basic' => 'blue',
            default => 'green',
        };
    }

    public function getBadgeClassAttribute()
    {
        $color = $this->badge_color;
        return "bg-{$color}-100 text-{$color}-800 border-{$color}-200";
    }

    public function getSubscribersCount()
    {
        return $this->activeSubscriptions()->count();
    }
}
