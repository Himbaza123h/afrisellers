<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AgentSubscription extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

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
        'amount_paid'     => 'decimal:2',
        'starts_at'       => 'datetime',
        'expires_at'      => 'datetime',
        'cancelled_at'    => 'datetime',
        'last_renewed_at' => 'datetime',
        'auto_renew'      => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function package()
    {
        return $this->belongsTo(AgentPackage::class, 'package_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────

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

    // Alias used in VendorController / SubscriptionController
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    // ── Status Helpers ────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // ── Time Helpers ──────────────────────────────────────────────────

    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return (int) now()->diffInDays($this->expires_at, false);
    }

    public function daysUsed(): int
    {
        return (int) $this->starts_at->diffInDays(now());
    }

    /**
     * Percentage of subscription period already consumed (0–100).
     * Used in the subscription index card progress bar.
     */
    public function percentUsed(): int
    {
        if (!$this->starts_at || !$this->expires_at) return 0;
        $total = $this->starts_at->diffInDays($this->expires_at) ?: 1;
        $used  = $this->starts_at->diffInDays(now());
        return (int) min(100, round(($used / $total) * 100));
    }

    /**
     * Alias kept for views that call getProgressPercentage().
     */
    public function getProgressPercentage(): int
    {
        return $this->percentUsed();
    }

    // ── Usage / Feature Guards ────────────────────────────────────────

    public function canAddReferral(): bool
    {
        return $this->isActive() &&
               $this->referrals_used < $this->package->max_referrals;
    }

    public function canRequestPayout(): bool
    {
        return $this->isActive() &&
               $this->payouts_used < $this->package->max_payouts_per_month;
    }

    public function hasAccessToRFQs(): bool
    {
        return $this->isActive() && $this->package->allow_rfqs;
    }

    public function incrementReferralUsage(): void
    {
        $this->increment('referrals_used');
    }

    public function incrementPayoutUsage(): void
    {
        $this->increment('payouts_used');
    }

    // ── Usage Percentages ─────────────────────────────────────────────

    public function getReferralUsagePercentage(): int
    {
        $max = $this->package->max_referrals;
        return $max > 0 ? (int) min(100, round(($this->referrals_used / $max) * 100)) : 0;
    }

    // ── Lifecycle Actions ─────────────────────────────────────────────

    public function activate(): void
    {
        $this->update([
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => now()->addDays($this->package->duration_days),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew'   => false,
        ]);
    }

    public function renew(): bool
    {
        if ($this->auto_renew && $this->isActive()) {
            $this->update([
                'starts_at'       => $this->expires_at,
                'expires_at'      => $this->expires_at->addDays($this->package->duration_days),
                'last_renewed_at' => now(),
                'referrals_used'  => 0,
                'payouts_used'    => 0,
            ]);
            return true;
        }
        return false;
    }

    // ── Invoice ───────────────────────────────────────────────────────

    public function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(substr(md5($this->id . $this->agent_id), 0, 8));
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active'    => 'bg-green-100 text-green-800',
            'expired'   => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'pending'   => 'bg-yellow-100 text-yellow-800',
            default     => 'bg-blue-100 text-blue-800',
        };
    }
}
