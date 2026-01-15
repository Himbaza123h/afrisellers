<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UserPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'amount_paid',
        'currency',
        'payment_method',
        'payment_reference',
        'paid_at',
        'products_used',
        'inquiries_used',
        'rfqs_used',
        'auto_renew',
        'next_billing_date',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'paid_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'amount_paid' => 'decimal:2',
        'auto_renew' => 'boolean',
        'products_used' => 'integer',
        'inquiries_used' => 'integer',
        'rfqs_used' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<=', now());
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount_paid, 2);
    }

    // Methods
    public function canAddProduct()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->plan->product_limit === -1) {
            return true; // Unlimited
        }

        return $this->products_used < $this->plan->product_limit;
    }

    public function incrementProductUsage()
    {
        $this->increment('products_used');
    }

    public function canSendInquiry()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->plan->buyer_inquiries_limit === -1) {
            return true;
        }

        return $this->inquiries_used < $this->plan->buyer_inquiries_limit;
    }

    public function incrementInquiryUsage()
    {
        $this->increment('inquiries_used');
    }

    public function canSendRfq()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->plan->buyer_rfqs_limit === -1) {
            return true;
        }

        return $this->rfqs_used < $this->plan->buyer_rfqs_limit;
    }

    public function incrementRfqUsage()
    {
        $this->increment('rfqs_used');
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $this->calculateExpiryDate(),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    private function calculateExpiryDate()
    {
        $billingCycle = $this->plan->billing_cycle;

        switch ($billingCycle) {
            case 'monthly':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            case 'yearly':
                return now()->addYear();
            case 'lifetime':
                return now()->addYears(100); // Effectively lifetime
            default:
                return now()->addMonth();
        }
    }
}
