<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'seller_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'is_trial',
        'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_trial' => 'boolean',
        'auto_renew' => 'boolean',
    ];

    /**
     * Relationship to User (Seller)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Relationship to Membership Plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }

    /**
     * Check if subscription is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    /**
     * Check if subscription has expired
     */
    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    /**
     * Get remaining days until expiration
     * Returns 0 if already expired
     */
    public function daysRemaining(): int
    {
        if ($this->ends_at->isPast()) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->ends_at, false));
    }

    /**
     * Check if subscription is past due (expired but still marked active)
     */
    public function isPastDue(): bool
    {
        return $this->ends_at->isPast() && $this->status === 'active';
    }

    /**
     * Check if subscription is expiring soon
     * @param int $days Number of days threshold (default 7)
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        $remaining = $this->daysRemaining();
        return $remaining > 0 && $remaining <= $days;
    }

    /**
     * Check if subscription is in trial period
     */
    public function isTrial(): bool
    {
        return $this->is_trial && $this->isActive();
    }

    /**
     * Check if subscription can be renewed
     */
    public function canBeRenewed(): bool
    {
        return $this->status === 'active' || $this->status === 'expired';
    }

    /**
     * Check if subscription can be upgraded
     */
    public function canBeUpgraded(): bool
    {
        return $this->status === 'active' && !$this->is_trial;
    }

    /**
     * Get subscription progress percentage
     */
    public function getProgressPercentage(): float
    {
        $totalDays = $this->starts_at->diffInDays($this->ends_at);
        if ($totalDays <= 0) {
            return 100;
        }

        $daysUsed = $this->starts_at->diffInDays(now());
        $percentage = min(($daysUsed / $totalDays) * 100, 100);

        return round($percentage, 1);
    }

    /**
     * Get human-readable time remaining
     */
    public function getTimeRemainingText(): string
    {
        $days = $this->daysRemaining();

        if ($days <= 0) {
            return 'Expired';
        }

        if ($days === 1) {
            return '1 day left';
        }

        if ($days <= 7) {
            return $days . ' days left';
        }

        $weeks = floor($days / 7);
        if ($weeks === 1) {
            return '1 week left';
        }

        if ($days <= 30) {
            return $weeks . ' weeks left';
        }

        $months = floor($days / 30);
        if ($months === 1) {
            return '1 month left';
        }

        return $months . ' months left';
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'active' => 'green',
            'trial' => 'purple',
            'expired' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope: Active subscriptions only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', now());
    }

    /**
     * Scope: Expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere(function($q) {
                        $q->where('status', 'active')
                          ->where('ends_at', '<=', now());
                    });
    }

    /**
     * Scope: Trial subscriptions
     */
    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    /**
     * Scope: Expiring soon
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', now())
                    ->where('ends_at', '<=', now()->addDays($days));
    }
}
