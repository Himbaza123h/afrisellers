<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Escrow extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'escrow_number',
        'transaction_id',
        'order_id',
        'buyer_id',
        'vendor_id',
        'amount',
        'currency',
        'platform_fee',
        'vendor_amount',
        'commission_amount',
        'status',
        'escrow_type',
        'release_condition',
        'auto_release_days',
        'release_date',
        'held_at',
        'released_at',
        'expected_release_at',
        'refunded_at',
        'buyer_approved',
        'buyer_approved_at',
        'vendor_confirmed',
        'vendor_confirmed_at',
        'admin_approved',
        'admin_approved_by',
        'admin_approved_at',
        'disputed',
        'dispute_reason',
        'dispute_opened_at',
        'dispute_resolved_at',
        'dispute_resolution',
        'payment_method',
        'payment_reference',
        'release_method',
        'release_reference',
        'terms',
        'conditions_met',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'expected_release_at' => 'datetime',
        'refunded_at' => 'datetime',
        'buyer_approved' => 'boolean',
        'buyer_approved_at' => 'datetime',
        'vendor_confirmed' => 'boolean',
        'vendor_confirmed_at' => 'datetime',
        'admin_approved' => 'boolean',
        'admin_approved_at' => 'datetime',
        'disputed' => 'boolean',
        'dispute_opened_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'conditions_met' => 'array',
        'metadata' => 'array',
        'release_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopeDisputed($query)
    {
        return $query->where('disputed', true);
    }

    public function scopeAwaitingRelease($query)
    {
        return $query->where('status', 'active')
                     ->where('buyer_approved', true)
                     ->where('admin_approved', false);
    }

    public function scopeAutoReleaseReady($query)
    {
        return $query->where('status', 'active')
                     ->where('release_condition', 'auto_release')
                     ->where('expected_release_at', '<=', now());
    }

    // Helper Methods
    public static function generateEscrowNumber()
    {
        $year = date('Y');
        $lastEscrow = self::whereYear('created_at', $year)
                         ->orderBy('id', 'desc')
                         ->first();

        $number = $lastEscrow ? intval(substr($lastEscrow->escrow_number, -4)) + 1 : 1;

        return 'ESC-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isReleased()
    {
        return $this->status === 'released';
    }

    public function isDisputed()
    {
        return $this->disputed === true;
    }

    public function canBeReleased()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->disputed) {
            return false;
        }

        switch ($this->release_condition) {
            case 'auto_release':
                return $this->expected_release_at <= now();
            case 'manual_approval':
                return $this->admin_approved;
            case 'delivery_confirmation':
                return $this->buyer_approved && $this->vendor_confirmed;
            case 'milestone_completion':
                return $this->buyer_approved;
            default:
                return false;
        }
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'held_at' => now(),
            'expected_release_at' => $this->auto_release_days
                ? now()->addDays($this->auto_release_days)
                : null,
        ]);
    }

    public function release($releaseMethod = null, $releaseReference = null, $notes = null)
    {
        if (!$this->canBeReleased()) {
            throw new \Exception('Escrow cannot be released at this time.');
        }

        $this->update([
            'status' => 'released',
            'released_at' => now(),
            'release_method' => $releaseMethod,
            'release_reference' => $releaseReference,
            'notes' => $notes ? ($this->notes . "\n\n" . $notes) : $this->notes,
        ]);

        return true;
    }

    public function refund($reason = null)
    {
        if ($this->status === 'released') {
            throw new \Exception('Cannot refund a released escrow.');
        }

        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'notes' => $reason ? ($this->notes . "\n\nRefund: " . $reason) : $this->notes,
        ]);

        return true;
    }

    public function openDispute($reason)
    {
        $this->update([
            'disputed' => true,
            'dispute_reason' => $reason,
            'dispute_opened_at' => now(),
        ]);
    }

    public function resolveDispute($resolution, $releaseToVendor = true)
    {
        $this->update([
            'disputed' => false,
            'dispute_resolved_at' => now(),
            'dispute_resolution' => $resolution,
        ]);

        if ($releaseToVendor) {
            $this->release(null, null, 'Dispute resolved in favor of vendor');
        } else {
            $this->refund('Dispute resolved in favor of buyer');
        }
    }

    public function buyerApprove()
    {
        $this->update([
            'buyer_approved' => true,
            'buyer_approved_at' => now(),
        ]);

        // Auto-release if conditions met
        if ($this->canBeReleased()) {
            $this->release();
        }
    }

    public function vendorConfirm()
    {
        $this->update([
            'vendor_confirmed' => true,
            'vendor_confirmed_at' => now(),
        ]);
    }

    public function adminApprove($adminId)
    {
        $this->update([
            'admin_approved' => true,
            'admin_approved_by' => $adminId,
            'admin_approved_at' => now(),
        ]);

        // Auto-release if conditions met
        if ($this->canBeReleased()) {
            $this->release();
        }
    }

    public function getDaysHeldAttribute()
    {
        if (!$this->held_at) {
            return 0;
        }

        $endDate = $this->released_at ?? $this->refunded_at ?? now();
        return $this->held_at->diffInDays($endDate);
    }

    public function getDaysUntilAutoReleaseAttribute()
    {
        if (!$this->expected_release_at || $this->status !== 'active') {
            return null;
        }

        return now()->diffInDays($this->expected_release_at, false);
    }

    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
