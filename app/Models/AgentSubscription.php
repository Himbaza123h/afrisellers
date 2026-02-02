<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AgentSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'package_id',
        'amount_paid',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'payment_method',
        'transaction_id',
        'payment_status',
        'referrals_used',
        'payouts_used',
        'auto_renew',
        'last_renewed_at',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_renewed_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    // Relationships
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function package()
    {
        return $this->belongsTo(AgentPackage::class, 'package_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function isExpired()
    {
        return $this->expires_at <= now();
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function daysRemaining()
    {
        if ($this->isExpired()) {
            return 0;
        }
        return now()->diffInDays($this->expires_at);
    }

    public function daysUsed()
    {
        return $this->starts_at->diffInDays(now());
    }

    public function canAddReferral()
    {
        return $this->isActive() &&
               $this->referrals_used < $this->package->max_referrals;
    }

    public function canRequestPayout()
    {
        return $this->isActive() &&
               $this->payouts_used < $this->package->max_payouts_per_month;
    }

    public function hasAccessToRFQs()
    {
        return $this->isActive() && $this->package->allow_rfqs;
    }

    public function incrementReferralUsage()
    {
        $this->increment('referrals_used');
    }

    public function incrementPayoutUsage()
    {
        $this->increment('payouts_used');
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($this->package->duration_days),
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

    public function renew()
    {
        if ($this->auto_renew && $this->isActive()) {
            $this->update([
                'starts_at' => $this->expires_at,
                'expires_at' => $this->expires_at->addDays($this->package->duration_days),
                'last_renewed_at' => now(),
                'referrals_used' => 0,
                'payouts_used' => 0,
            ]);
            return true;
        }
        return false;
    }

    public function getProgressPercentage()
    {
        $total = $this->starts_at->diffInDays($this->expires_at);
        $used = $this->starts_at->diffInDays(now());
        return $total > 0 ? min(100, round(($used / $total) * 100)) : 0;
    }

    public function getReferralUsagePercentage()
    {
        $max = $this->package->max_referrals;
        return $max > 0 ? min(100, round(($this->referrals_used / $max) * 100)) : 0;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-blue-100 text-blue-800',
        };
    }
}
